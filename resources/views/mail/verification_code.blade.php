@php use Carbon\Carbon; @endphp
<br/> Dear {{ $receiver_name }}
<br/>
<br/>  Here is the code {{ $code ?: '-' }}. Use this to verify your account.
<br/>

<br/> Thank you
<br/> Regards
<br/> AdBarta
<br/>
<p class="text-center" style="color: #b09464; font-size:15px"><i>
        Copyright © {{ Carbon::now()->format('Y') }} AdBarta, All rights reserved.</i>
</p>
