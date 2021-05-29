/**
* @file
*/

(function ($, Drupal) {

  Drupal.AjaxCommands.prototype.episodeUpdate = function (ajax, response, status) {
    $(window).trigger('imdb_api:episode:update', [response.data]);
  }

  Drupal.AjaxCommands.prototype.episodeDelete = function (ajax, response, status) {
    $(window).trigger('imdb_api:episode:delete', [response.data]);
  }

})(jQuery, Drupal);
