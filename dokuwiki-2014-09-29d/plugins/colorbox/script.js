/* DOKUWIKI:include jquery.colorbox.js */
jQuery(function() {
    jQuery('a[class=media][href]').each(function() {
        var $self = jQuery(this);
        var img =  $self.find('img');
        if (!img.length) {
            return false;
        };
        var ppath = "lib/exe/fetch.php?media=";
        if ($self.attr("href").indexOf(ppath) != -1) {
            // Points directly to a media item
            //console.log ( "image href: ".concat($self.attr("href")) );
            //console.log ( "image img src: ".concat(img.attr("src")));
            if (! $self.attr("title") ) $self.attr("title",img.attr("alt"));
            img.attr("url",$self.attr("href"));
            $self.attr("surl",img.attr("src"));
            $self.attr("furl",$self.attr("href"));
            $self.attr("exif",img.attr("exif"));
            $self.attr("map",img.attr("map"));
            $self.attr("href",img.attr("url").replace("fetch.php?","fetch.php?w=1360&tok=".concat(img.attr("token")).concat('&')));
            $self.attr("murl",$self.attr("href"));
            //console.log ( "image href: ".concat($self.attr("href")) );
            $self.attr("rel", "colorbox[gallery]");
            //console.log ( "image url:".concat($self.attr("surl")));
            //console.log ( "image url:".concat($self.attr("murl")));
            //console.log ( "image url:".concat($self.attr("furl")));
            //console.log ( "image exif:".concat($self.attr("exif")));
            //console.log ( "image map:".concat($self.attr("map")));
        };
    });
    jQuery('div[class^="gallery gallery_"]').each(function() {
        var $gallery = jQuery(this);
        var links = $gallery.find('a');
        var imgs =  $gallery.find('img');
        if (!imgs.length) {
            return false;
        };
        var ppath = "lib/exe/fetch.php?media=";
        for (i=0; i < imgs.length; i++) {
            if (links.eq(i).attr("href").indexOf(ppath) != -1) {
                // Points directly to a media item
                //console.log ( "gallery index: ".concat(i) );
                //console.log ( "gallery href: ".concat(links.eq(i).attr("href")) );
                //console.log ( "gallery img src: ".concat(imgs.eq(i).attr("src")));
                if (! links.eq(i).attr("title")  ) links.eq(i).attr("title",imgs.eq(i).attr("alt"));
                imgs.eq(i).attr("url",links.eq(i).attr("href"));
                links.eq(i).attr("surl",imgs.eq(i).attr("src"));
                links.eq(i).attr("furl",links.eq(i).attr("href"));
                links.eq(i).attr("exif",imgs.eq(i).attr("exif"));
                links.eq(i).attr("map",imgs.eq(i).attr("map"));
                links.eq(i).attr("href",imgs.eq(i).attr("url").replace("fetch.php?","fetch.php?w=1360&tok=".concat(imgs.eq(i).attr("token")).concat('&')));
                links.eq(i).attr("murl",links.eq(i).attr("href"));
                links.eq(i).attr("rel", "colorbox[gallery]");
                //console.log ( "gallery href: ".concat(links.eq(i).attr("href")) );
                //console.log ( "gallery url:".concat(links.eq(i).attr("url")));
                //console.log ( "gallery exif:".concat(links.eq(i).attr("exif")));
                //console.log ( "gallery map:".concat(links.eq(i).attr("map")));
            };
        }
    });
    //jQuery("a[rel^='colorbox']").colorbox({ opacity:0.6 , maxwidth:'100%', maxheight:'100%', rel:'group1'});
    //jQuery("a[rel^='colorbox']").colorbox({ opacity:0.6 , width:'100%', height:'100%', rel:'group1', slideshow: 'true', slideshowAuto: 'false'});
    jQuery("a[rel^='colorbox']").colorbox({ opacity:0.8 , width:'100%', height:'100%', rel:'group1'});
    //jQuery('a.media').colorbox();
    //jQuery(document).ready(function () {
    //            jQuery("a[rel^='colorbox']").colorbox({ opacity:0.6 , rel:'group1' });
    //        });
});
