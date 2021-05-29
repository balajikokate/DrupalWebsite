(function($, Drupal) {

  Drupal.behaviors.imdbApiEpisodeWidget = {
    attach: function attach(context, settings) {

      $(window).on('imdb_api:episode:update', function (event, data) {
        $('input.episode-field-' + JSON.parse(data).delta).val(data);
      });

      $(window).on('imdb_api:episode:delete', function (event, data) {
        var $episode = $('input.episode-field-' + data);

        $episode.val('');
        $episode.closest('tr.draggable').addClass('visually-hidden');

        $('input.episode-removed-' + data).attr('checked', 'checked');
      });

      $('input.episode-removed[checked="checked"]').closest('tr.draggable').addClass('visually-hidden');
    }
  };

})(jQuery, Drupal);
