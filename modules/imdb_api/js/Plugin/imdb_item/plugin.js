(function($, Drupal, CKEDITOR) {

  CKEDITOR.plugins.add('imdbapiitem', {
    icons: 'imdbapiitem',
    init: function init(editor) {
      editor.addCommand('imdbapiitem', {
        exec: function (editor) {
          const saveCallback = function(values) {
            editor.insertHtml(values.output);
          }

          Drupal.ckeditor.openDialog(editor, Drupal.url('imdb-api/item/dialog'), {}, saveCallback, {});
        }
      });
      editor.ui.addButton('ImdbApiItem', {
        label: 'IMDB API Item',
        command: 'imdbapiitem',
        toolbar: 'insert',
      });
    }
  });

})(jQuery, Drupal, CKEDITOR);
