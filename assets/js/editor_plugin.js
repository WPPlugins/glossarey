(function() {
  tinymce.create( 'tinymce.plugins.GlossareyPlugin', {
    init: function(ed, url) {
      ed.addButton('glossarey', {
        title   : "Glossarey term",
        image   : url.replace('js', 'img') + '/dictionary20x20.png',
        onclick : function(){
          var termID = prompt("Glossary term", "Enter the glossary term which description will be shown..");
          ed.execCommand('mceInsertContent', false, '[term keyword="' + termID + '"]insert_word_here[/term]');
        }
      });
    },
    createControl : function(n,cm){
      return null;
    },
    getInfo : function() {
      return {
        longname : 'Example plugin',
        author: 'Lubomir Herko',
        authorurl: 'https://www.facebook.com/lubomir.herko',
        infourl: 'https://www.facebook.com/lubomir.herko',
        version: '0.1'
      };
    }
  });
  tinymce.PluginManager.add('glossarey', tinymce.plugins.GlossareyPlugin);
})();