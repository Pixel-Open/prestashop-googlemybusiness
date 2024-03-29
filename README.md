# Pixel GoogleMyBusiness

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-green)](https://php.net/)
[![Minimum Prestashop Version](https://img.shields.io/badge/prestashop-%3E%3D%201.7.6.0-green)](https://www.prestashop.com)
[![GitHub release](https://img.shields.io/github/v/release/Pixel-Open/prestashop-googlemybusiness)](https://github.com/Pixel-Open/prestashop-googlemybusiness/releases)

## Presentation

GoogleMyBusiness is a Prestashop module to import and display any Google place data on the frontend.

![Place display](screenshot.png)

## Requirements

- Prestashop >= 1.7.6.0
- PHP >= 7.2.0

## Installation

Download the **pixel_googlemybusiness.zip** file from the [last release](https://github.com/Pixel-Open/prestashop-googlemybusiness/releases/latest) assets.

### Admin

Go to the admin module catalog section and click **Upload a module**. Select the downloaded zip file.

### Manually

Move the downloaded file in the Prestashop **modules** directory and unzip the archive. Go to the admin module catalog section and search for "Google My Business".

## Configuration

From the module manager, find the module and click on configure.

| Field             | Description                                  | Example                          | Required |
|:------------------|:---------------------------------------------|----------------------------------|----------|
| Google API Key    | The Google Places API key.                   | D5MyLvpGOTI2GYXpisyJQCKw9ED3wdk7 | Y        |
| Google Place IDs  | Google place ids to import, one id per line. | ChIJLU7jZClu5kcR4PcOOO6p3I0      | Y        |

## Import

At the command prompt, go to the Prestashop root directory and  execute the following command:

```bash
./bin/console google_my_business:import_place {language}
```

- *language*: ISO 639-1 (en, de, fr...)

## Reviews

It is only possible to retrieve the last 5 reviews. Import often to accumulate the reviews.

## Display

In any template, add the following Widget:

```smarty
{widget name='pixel_googlemybusiness' display='name,phone,rating,opening-hours,reviews'}
```

**Display excepted options:**

* name: Place name (Eiffel tower)
* phone: International phone number (+33 1 22 33 44 55)
* rating: Average Rating (4/5)
* opening-hours: Opening hours (Monday: 12:00 – 19:00, Tuesday: 10:00 – 19:00...)
* review: the last reviews

For example, to display only reviews:

```smarty
{widget name='pixel_googlemybusiness' display='reviews'}
```

**Review filters:**

* review_number: the number of review to display
* review_min_rating: only show reviews with rating greater or equal than this value

```smarty
{widget name='pixel_googlemybusiness' display='reviews' review_number=5 review_min_rating=3}
```

**Place filter:**

Filter by place id with the `place_ids` widget param (comma separated):

```smarty
{widget name='pixel_googlemybusiness' place_ids='ChIJLU7jZClu5kcR4PcOOO6p3I0' display='name,rating,opening-hours,reviews'}
```

**Note:** Only places imported in the current context language will be displayed

## Custom template

You can create your own template to display places.

Create a new template file in the **pixel_googlemybusiness** directory for your theme:

*themes/{themeName}/modules/pixel_googlemybusiness/custom.tpl*

```smarty
{foreach from=$places item=place}
    {$place->getPlaceId()}
    {$place->getName()}
    {$place->getRating()}
    {$place->getUserRatingsTotal()}
    {foreach from=$place->getOpeningHoursWeekdayText()|json_decode:1 item=hour}
        {$hour}
    {/foreach}
    {foreach from=$place->getReviews() item=review}
        {$review->getAuthorName()}
        {$review->getTime()|date_format:"%e %B %Y"}
        {$review->getRating()}
        {if $review->getComment()}
            {$review->getComment()}
        {/if}
    {/foreach}
{/foreach}
```

Add the **template** option in the widget with the template path:

```smarty
{widget name='pixel_googlemybusiness' template='module:pixel_googlemybusiness/custom.tpl'}
```

## Translations

In admin, go to the **Translations** page under the **International** menu.