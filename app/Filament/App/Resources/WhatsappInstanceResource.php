<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WhatsappInstanceResource\RelationManagers\InstanceTypebotsRelationManager;
use App\Filament\App\Resources\WhatsappInstanceResource\{Pages};
use App\Models\{WhatsappInstance};
use App\Services\Evolution\Instance\{ConnectEvolutionInstanceService, DeleteEvolutionInstanceService, LogOutEvolutionInstanceService};
use App\Services\Evolution\Instance\{FetchEvolutionInstanceService, RestartEvolutionInstanceService};
use App\Services\Evolution\Message\SendMessageEvolutionService;
use Filament\Facades\Filament;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\{Section, TextInput, ToggleButtons};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{Action, ActionGroup, DeleteAction, EditAction, ViewAction};
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Table;
use Leandrocfe\FilamentPtbrFormFields\PhoneNumber;

class WhatsappInstanceResource extends Resource
{
    protected static ?string $model = WhatsappInstance::class;

    protected static ?string $navigationIcon = 'fab-whatsapp';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Instâncias WhatsApp';

    protected static ?string $modelLabel = 'Instâncias WhatsApp';

    protected static ?string $modelLabelPlural = "Instâncias WhatsApp";

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados da Instância')
                    ->schema([

                        TextInput::make('name')
                            ->label('Nome da Instância')
                            ->unique(WhatsappInstance::class, 'name', ignoreRecord: true)
                            ->default(fn () => Filament::getTenant()?->slug ?? '')
                            ->required()
                            ->prefixIcon('fas-id-card')
                            ->validationMessages([
                                'unique' => 'Nome da instância já cadastrada.',
                            ])
                            ->maxLength(20),

                        PhoneNumber::make('number')
                            ->label('Número WhatsApp')
                            ->unique(WhatsappInstance::class, 'number', ignoreRecord: true)
                            ->mask('+55 (99) 99999-9999')
                            ->placeholder('+55 (99) 99999-9999')
                            ->required()
                            ->prefixIcon('fab-whatsapp')
                            ->validationMessages([
                                'unique' => 'Número já cadastrado.',
                            ]),

                    ])->columns(2),

                Section::make('Dados da Instância')
                    ->schema([
                        ToggleButtons::make('groups_ignore')
                            ->label('Ignorar Grupos')
                            ->inline()
                            ->boolean()
                            ->required(),

                        ToggleButtons::make('always_online')
                            ->label('Status Sempre Online')
                            ->inline()
                            ->boolean()
                            ->required(),

                        ToggleButtons::make('read_messages')
                            ->label('Marcar Mensagens como Lidas')
                            ->inline()
                            ->boolean()
                            ->required(),

                        ToggleButtons::make('read_status')
                            ->label('Marcar Status como Lido')
                            ->inline()
                            ->boolean()
                            ->required(),

                        ToggleButtons::make('sync_full_history')
                            ->label('Sincronizar Histórico')
                            ->inline()
                            ->boolean()
                            ->required(),

                        ToggleButtons::make('reject_call')
                            ->label('Rejeitar Chamadas')
                            ->inline()
                            ->boolean()
                            ->live()
                            ->reactive()
                            ->required(),

                        TextInput::make('msg_call')
                            ->label('Mensagem para Chamadas Rejeitadas')
                            ->required()
                            ->hidden(fn ($get) => $get('reject_call') == false)
                            ->maxLength(255),

                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_picture_url')
                    ->label('Imagem de Perfil')
                    ->alignCenter()
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->profile_picture_url ?: 'https://www.cidademarketing.com.br/marketing/wp-content/uploads/2018/12/whatsapp-640x640.png'),

                TextColumn::make('status')
                    ->label('Status')
                    ->alignCenter()
                    ->badge()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nome da Instância')
                    ->searchable(),

                TextColumn::make('number')
                    ->label('Número')
                    ->searchable(),

                TextColumn::make('instance_id')
                    ->label('ID da Instância')
                    ->searchable(),

                TextColumn::make('bots_count')
                    ->label('Quantidade de Robôs')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->typebots()->where('is_active', true)->count() ?? 0)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('showQr')
                    ->hidden(fn ($record) => $record->status->value === 'open')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading('Qr Code WhatsApp')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(
                        \Filament\Actions\Action::make('close')
                            ->label('FECHAR')
                            ->color('danger') // Cores: primary, secondary, success, danger, warning, gray
                            ->extraAttributes(['class' => 'w-full']) // Largura total
                            ->close()
                    )
                    ->modalWidth('md') // ou sm, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
                    ->modalContent(fn ($record) => view('evolution.qr-code-modal', [
                        'qrCode' => str_replace('\/', '/', $record->getRawOriginal('qr_code')),
                    ])),

                ActionGroup::make([
                    Action::make('RestartInstance')
                        ->label('Reiniciar Instância')
                        ->hidden(fn ($record) => $record->status->value === 'close')
                        ->icon('fas-rotate-right')
                        ->color('warning')
                        ->action(function ($record, $livewire) {
                            $service  = new RestartEvolutionInstanceService();
                            $response = $service->restartInstance($record->name);

                            if (isset($response['error'])) {
                                Notification::make()
                                    ->title('Erro ao reiniciar')
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Instância reiniciada')
                                    ->success()
                                    ->send();
                            }
                            $livewire->dispatch('refresh');
                        }),

                    Action::make('LogoutInstance')
                        ->hidden(fn ($record) => $record->status->value !== 'open')
                        ->label('Desconectar Instância')
                        ->icon('fas-sign-out-alt')
                        ->color('danger')
                        ->action(function ($record, $livewire) {
                            $service  = new LogOutEvolutionInstanceService();
                            $response = $service->logoutInstance($record->name);

                            if (!empty($response['error'])) {
                                Notification::make()
                                    ->title('Erro ao desconectar')
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Instância desconectada')
                                    ->body('Faça login novamente e escaneie o QR Code')
                                    ->success()
                                    ->send();
                            }
                            $livewire->dispatch('refresh');
                        }),

                    Action::make('ConectInstance')
                        ->hidden(fn ($record) => $record->status->value === 'open')
                        ->label('Conectar Instância')
                        ->icon('fas-sign-in-alt')
                        ->color('info')
                        ->action(function ($record, $livewire) {
                            $service  = new ConnectEvolutionInstanceService();
                            $response = $service->connectInstance($record->name);

                            if (isset($response['error'])) {
                                Notification::make()
                                    ->title('Erro ao reconectar')
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Instância reconectada')
                                    ->body('Leia o QRcode para Ativar Sincronização dos dados')
                                    ->success()
                                    ->send();
                            }
                            $livewire->dispatch('refresh');
                        }),

                    Action::make('syncInstance')
                        ->label('Sincronizar Dados')
                        ->icon('fas-sync')
                        ->color('info')
                        ->action(function ($record, $livewire) {
                            $service  = new FetchEvolutionInstanceService();
                            $response = $service->fetchInstance($record->name);

                            if (isset($response['error'])) {
                                Notification::make()
                                    ->title('Erro ao sincronizar dados')
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Instância sincronizada')
                                    ->body('Dados sincronizados com sucesso')
                                    ->success()
                                    ->send();
                            }
                            // Fecha o ActionGroup
                            $livewire->dispatch('close-modal');
                            $livewire->dispatch('refresh');
                        }),

                    Action::make('Enviar Mensagem')
                        ->requiresConfirmation()
                        ->hidden(fn ($record) => $record->status->value !== 'open')
                        ->form([
                            Fieldset::make('Envie sua mensagem')
                                ->schema([
                                    PhoneNumber::make('number_whatsapp')
                                        ->label('Número WhatsApp')
                                        ->mask('+55 (99) 99999-9999')
                                        ->placeholder('+55 (99) 99999-9999')
                                        ->required()
                                        ->prefixIcon('fab-whatsapp'),

                                    TextInput::make('message')
                                        ->label('Mensagem'),

                                ])->columns(1),
                        ])

                        ->modalHeading('Enviar Mensagem')
                        ->modalDescription('Envie uma de teste para validar o serviço')
                        ->color('success')
                        ->icon('fab-whatsapp')
                        ->action(function (Action $action, $record, array $data, $livewire) {
                            try {
                                $service = new SendMessageEvolutionService();
                                $service->sendMessage($record->name, $data);

                                Notification::make()
                                    ->title('Mensagem enviada')
                                    ->body('Mensagem enviada com Sucesso')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Erro ao enviar mensagem')
                                    ->body('Ocorreu um erro ao enviar mensagem: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                            $livewire->dispatch('refresh');
                        })
                        ->icon('fab-whatsapp')
                        ->color('success'),
                ])
                    ->icon('fab-whatsapp')
                    ->color('success'),

                ActionGroup::make([
                    ViewAction::make()
                        ->color('primary'),
                    EditAction::make()
                        ->color('secondary'),
                    DeleteAction::make()
                        ->action(function ($record, $livewire) {
                            $service  = new DeleteEvolutionInstanceService();
                            $response = $service->deleteInstance($record->name);

                            // Deleta o registro local após sucesso na API
                            $record->delete();
                            $livewire->dispatch('refresh');
                        }),
                ])
                    ->icon('fas-sliders')
                    ->color('warning'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            InstanceTypebotsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWhatsappInstances::route('/'),
            'create' => Pages\CreateWhatsappInstance::route('/create'),
            'view'   => Pages\ViewWhatsappInstance::route('/{record}'),
            'edit'   => Pages\EditWhatsappInstance::route('/{record}/edit'),
        ];
    }
}
