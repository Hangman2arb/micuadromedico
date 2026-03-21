{{-- FAQ Accordion component
     Usage: @include('components.faq-accordion', ['faqs' => [['question' => '...', 'answer' => '...'], ...], 'title' => 'Preguntas Frecuentes'])
     Includes FAQPage Schema.org markup automatically.
--}}
@if(!empty($faqs))
<section class="mt-12 lg:mt-16" id="preguntas-frecuentes">
    <h2 class="text-2xl lg:text-3xl font-bold text-ink mb-6">
        <i class="fa-solid fa-circle-question text-primary mr-2 text-xl lg:text-2xl"></i>{{ $title ?? 'Preguntas Frecuentes' }}
    </h2>

    <div class="space-y-3" x-data="{ openFaq: null }">
        @foreach($faqs as $i => $faq)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden transition-shadow hover:shadow-sm">
            <button
                @click="openFaq = openFaq === {{ $i }} ? null : {{ $i }}"
                class="w-full flex items-center justify-between px-5 py-4 text-left gap-4"
                :aria-expanded="openFaq === {{ $i }}"
                aria-controls="faq-answer-{{ $i }}"
            >
                <span class="font-semibold text-ink text-[15px] leading-snug">{{ $faq['question'] }}</span>
                <i class="fa-solid fa-chevron-down text-primary text-xs shrink-0 transition-transform duration-300"
                   :class="openFaq === {{ $i }} ? 'rotate-180' : ''"></i>
            </button>
            <div
                id="faq-answer-{{ $i }}"
                x-show="openFaq === {{ $i }}"
                x-cloak
                x-collapse
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            >
                <div class="px-5 pb-5 text-gray-600 text-[15px] leading-relaxed border-t border-gray-100 pt-4 prose prose-sm max-w-none">
                    {!! $faq['answer'] !!}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- FAQPage Schema --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            @foreach($faqs as $i => $faq)
            {
                "@@type": "Question",
                "name": @json($faq['question']),
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": @json(strip_tags($faq['answer']))
                }
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
</section>
@endif
