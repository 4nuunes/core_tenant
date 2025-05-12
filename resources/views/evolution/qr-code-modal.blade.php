
@if(!$qrCode)
<div class="flex justify-center">
    <p class="text-xl font-bold text-center text-black">QrCode inexistente, reconecte a instância</p>
</div>
@endif

@if($qrCode)
<div class="mb-4 text-center">
    <p class="text-xl font-bold text-black">Escaneie o QR Code no seu WhatsApp para ativar a Instância</p>
</div>

<div class="flex justify-center">

        <img src="{{ $qrCode }}" alt="QR Code" class="object-contain w-30 h-30">

</div>
@endif



