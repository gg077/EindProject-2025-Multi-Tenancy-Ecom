@php use Illuminate\Support\Facades\Auth; @endphp
<div>
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
            {{ session('error') }}
        </div>
    @endif

        {{-- Als gebruiker al een review heeft ingediend --}}
    @if ($alreadyReviewed)
        <div class="p-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800">
            <p class="font-semibold">You have already reviewed this product for this order.</p>
            {{-- Haal bestaande review op om te tonen --}}
            @php
                $existingReview = Auth::check() ? \App\Models\Review::where('user_id', Auth::id())->where('product_id', $product->id)->where('order_id', $order->id)->first() : null;
            @endphp
            {{--  Toon inhoud van de bestaande review --}}
        @if ($existingReview)
                <div class="mt-2">
                    <p><strong>Your Rating:</strong> {{ $existingReview->rating }} / 5 Stars</p>
                    <p><strong>Your Comment:</strong></p>
                    <p class="pl-2 border-l-2 border-gray-300">{{ $existingReview->comment }}</p>
                </div>
            @endif
        </div>
            {{-- Als bestelling niet beoordeeld mag worden (nog niet betaald) --}}
        @elseif (!$orderAllowsReview)
        <div class="p-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-200 dark:text-yellow-800">
            <p class="font-semibold">Reviews can only be submitted for completed or paid orders.</p>
        </div>
            {{-- Reviewformulier tonen als alles toegestaan is --}}
        @else
            {{-- x-data = AlpineJS data voor sterrenrating --}}
        <form wire:submit.prevent="saveReview" x-data="{ rating: @entangle('rating'), hoverRating: 0, maxStars: 5 }">
            {{-- Sterrenrating veld --}}
            <div class="mb-4">
                <label for="rating" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Rating</label>
                <div class="flex items-center">
                    {{-- Loop over het aantal sterren met AlpineJS --}}
                    <template x-for="star in maxStars" :key="star">
                        <svg @click="rating = star"
                             @mouseenter="hoverRating = star"
                             @mouseleave="hoverRating = 0"
                             :class="{'text-yellow-400': hoverRating >= star || rating >= star, 'text-gray-300 dark:text-gray-500': hoverRating < star && rating < star }"
                             class="w-8 h-8 cursor-pointer transition-colors duration-150 ease-in-out"
                             fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </template>
                </div>
                @error('rating') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Commentaarveld --}}
            <div class="mb-4">
                <label for="comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Comment</label>
                <textarea wire:model.defer="comment" id="comment" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your review here..."></textarea>
                @error('comment') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Verstuurknop --}}
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Submit Review
            </button>
        </form>
    @endif
</div>
