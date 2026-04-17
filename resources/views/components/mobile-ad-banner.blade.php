@props(['ad'])

@php
    $mobileSrc = $ad->mobile_image_url ?: $ad->image_url;
@endphp

<div class="overflow-hidden">
    @if($ad->type === 'html' && $ad->html_code)
        {!! $ad->html_code !!}
    @elseif($mobileSrc)
        @if($ad->target_url)
            <a href="{{ route('ads.click', $ad) }}" target="_blank" rel="nofollow sponsored noopener" class="block">
                <img src="{{ $mobileSrc }}" alt="{{ $ad->alt_text ?: $ad->title }}" class="w-full h-[90px] sm:h-[100px] object-cover">
            </a>
        @else
            <img src="{{ $mobileSrc }}" alt="{{ $ad->alt_text ?: $ad->title }}" class="w-full h-[90px] sm:h-[100px] object-cover">
        @endif
    @endif
</div>
