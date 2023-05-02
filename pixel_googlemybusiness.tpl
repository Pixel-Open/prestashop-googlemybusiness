<div class="google-places">
    {foreach from=$places item=place}
    <div class="place" itemscope itemtype="https://schema.org/Place">
        {if 'name'|in_array:$display}
            <div class="name" itemprop="name">{$place->getName()}</div>
            <div><a href="tel:{$place->getPhone()}"><i
                            class="material-icons">&#xE0B0;</i><span
                            itemprop="telephone">{$place->getPhone()}</span></a></div>
        {/if}
        {if 'rating'|in_array:$display}
            <div class="rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                {if $place->getUserRatingsTotal()}
                    <meta itemprop="ratingValue" content="{$place->getRating()}"/>
                    <meta itemprop="reviewCount" content="{$place->getUserRatingsTotal()}">
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
                <li itemprop="openingHours">{$hour}</li>
                {/foreach}
            </ul>
        {/if}
        {if 'reviews'|in_array:$display}
            <div class="reviews">
                {foreach from=$place->getReviews() item=review}
                    <div class="review" itemprop="review" itemscope itemtype="https://schema.org/Review">
                        <a href="{$review->getAuthorUrl()}" title="{$review->getAuthorName()}"><span class="author" itemprop="author">{$review->getAuthorName()}</span></a>
                        <span class="date" itemprop="datePublished" content="{$review->getTime()|date_format:"%Y-%m-%d"}">{$review->getTime()|date_format:"%e %B %Y"}</span>
                        <span class="note" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                            {$review->getRating()}/5
                            <meta itemprop="worstRating" content = "1">
                            <meta itemprop="ratingValue" content="{$review->getRating()}"/>
                            <meta itemprop="bestRating" content = "5">
                        </span>
                        {if $review->getComment()}
                            <div class="comment" itemprop="reviewBody">
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