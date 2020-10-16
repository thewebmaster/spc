$(document).ready(function(){
    var swapiErrorMessage = 'An error has been encountered with Swapi API. Please try again.';

    if ($('.get-character').length) {
        $('.get-character').each(function(){
            var $currentDiv = $(this);
            $.get($currentDiv.attr('data-url'), function(data) {
                if (data.name) {
                    $currentDiv.html(data.name);
                } else {
                    $currentDiv.html(swapiErrorMessage);
                }
            });
        });
    }

    if ($('.get-species').length) {
        $('.get-species').each(function(){
            var $currentDiv = $(this);
            $.get($currentDiv.attr('data-url'), function(data) {
                if (data.name) {
                    if (data.classification === 'mammal') {
                        $.get(data.homeworld, function(homeworld) {
                            var homeworldContent = '';
                            if (homeworld.name) {
                                homeworldContent = homeworld.name;
                            } else {
                                homeworldContent = swapiErrorMessage;
                            }
                            $currentDiv.html('<code>Name: ' + data.name + ' | Homeworld: '+ homeworldContent + '</code>');
                        });
                    } else {
                        $currentDiv.remove();
                    }
                } else {
                    $currentDiv.html(swapiErrorMessage);
                }
            });
        });
    }
});