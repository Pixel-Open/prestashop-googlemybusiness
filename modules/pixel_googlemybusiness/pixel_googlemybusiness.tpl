<div class="google-places">
    {foreach from=$places item=place}
    <div class="place">
        <div class="name">{$place->getName()}</div>
        <div class="rating">
            {if $place->getUserRatingsTotal()}
                {l s='Note:' d='Modules.Pixelgooglemybusiness.Shop'} <strong>{$place->getRating()}/5</strong> ({$place->getUserRatingsTotal()} {l s='reviews' d='Modules.Pixelgooglemybusiness.Shop'})
            {else}
                <a href="https://www.google.com/maps/search/?api=1&query=Google&query_place_id={$place->getPlaceId()}" target="_blank" rel="noopener noreferrer">
                    {l s='Be the first to post a review' d='Modules.Pixelgooglemybusiness.Shop'}
                </a>
            {/if}
        </div>
        <ul class="opening-hours">
            {foreach from=$place->getOpeningHoursWeekdayText()|json_decode:1 item=hour}
            <li>{$hour}</li>
            {/foreach}
        </ul>
    </div>
    {/foreach}
</div>