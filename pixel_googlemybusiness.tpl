<div class="google-places">
    {foreach from=$places item=place}
    <div class="place">
        {if 'name'|in_array:$display}
            <div class="name">{$place->getName()}</div>
        {/if}
        {if 'rating'|in_array:$display}
            <div class="rating">
                {if $place->getUserRatingsTotal()}
                    {l s='Note:' d='Modules.Pixelgooglemybusiness.Shop'} <strong>{$place->getRating()}/5</strong> ({$place->getUserRatingsTotal()} {l s='reviews' d='Modules.Pixelgooglemybusiness.Shop'})
                {else}
                    <a href="https://www.google.com/maps/search/?api=1&query=Google&query_place_id={$place->getPlaceId()}" target="_blank" rel="noopener noreferrer">
                        {l s='Be the first to post a review' d='Modules.Pixelgooglemybusiness.Shop'}
                    </a>
                {/if}
            </div>
        {/if}
        {if 'opening-hours'|in_array:$display}
            <ul class="opening-hours">
                {foreach from=$place->getOpeningHoursWeekdayText()|json_decode:1 item=hour}
                <li>{$hour}</li>
                {/foreach}
            </ul>
        {/if}
        {if 'reviews'|in_array:$display}
            <div class="reviews">
                {foreach from=$place->getReviews() item=review}
                    <div class="review">
                        <span class="author">{$review->getAuthorName()}</span>
                        <span class="date">{$review->getTime()|date_format:"%e %B %Y"}</span>
                        <span class="note">{$review->getRating()}/5</span>
                        {if $review->getComment()}
                            <div class="comment">
                                {$review->getComment()}
                            </div>
                        {/if}
                    </div>
                {/foreach}
            </div>
        {/if}
    </div>
    {/foreach}
</div>