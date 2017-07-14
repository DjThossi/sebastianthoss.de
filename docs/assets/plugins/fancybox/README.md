<h1 id="fancybox">fancyBox</h1>

<p>fancyBox is a tool that offers a nice and elegant way to add zooming functionality for images, html content and multi-media on your webpages.</p>

<p>More information and examples: http://www.fancyapps.com/fancybox/</p>

<p>License: http://www.fancyapps.com/fancybox/#license</p>

<p>Copyright (c) 2012 Janis Skarnelis - janis@fancyapps.com</p>

<h2 id="how-to-use">How to use</h2>

<p>To get started, download the plugin, unzip it and copy files to your website/application directory.
Load files in the <head> section of your HTML document. Make sure you also add the jQuery library.</p>

<pre><code>&lt;head&gt;
    &lt;script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"&gt;&lt;/script&gt;
    &lt;link rel="stylesheet" href="/fancybox/jquery.fancybox.css" type="text/css" media="screen" /&gt;
    &lt;script type="text/javascript" src="/fancybox/jquery.fancybox.pack.js"&gt;&lt;/script&gt;
&lt;/head&gt;
</code></pre>

<p>Create your links with a <code>title</code> if you want a title to be shown, and add a class:</p>

<pre><code>&lt;a href="large_image.jpg" class="fancybox" title="Sample title"&gt;&lt;img src="small_image.jpg" /&gt;&lt;/a&gt;
</code></pre>

<p>If you have a set of related items that you would like to group,
additionally include a group name in the <code>rel</code> (or <code>data-fancybox-group</code>) attribute:</p>

<pre><code>&lt;a href="large_1.jpg" class="fancybox" rel="gallery" title="Sample title 1"&gt;&lt;img src="small_1.jpg" /&gt;&lt;/a&gt;
&lt;a href="large_2.jpg" class="fancybox" rel="gallery" title="Sample title 1"&gt;&lt;img src="small_2.jpg" /&gt;&lt;/a&gt;
</code></pre>

<p>Initialise the script like this:</p>

<pre><code>&lt;script&gt;
    $(document).ready(function() {
        $('.fancybox').fancybox();
    });
&lt;/script&gt;
</code></pre>

<p>May also be passed an optional options object which will extend the default values. Example:</p>

<pre><code>&lt;script&gt;
    $(document).ready(function() {
        $('.fancybox').fancybox({
            padding : 0,
            openEffect  : 'elastic'
        });
    });
&lt;/script&gt;
</code></pre>

<p>Tip: Automatically group and apply fancyBox to all images:</p>

<pre><code>$("a[href$='.jpg'],a[href$='.jpeg'],a[href$='.png'],a[href$='.gif']").attr('rel', 'gallery').fancybox();
</code></pre>

<p>Script uses the <code>href</code> attribute of the matched elements to obtain the location of the content and to figure out content type you want to display.
You can specify type directly by adding classname (fancybox.image, fancybox.iframe, etc) or <code>data-fancybox-type</code> attribute:</p>

<pre><code>//Ajax:
&lt;a href="/example.html" class="fancybox fancybox.ajax"&gt;Example&lt;/a&gt;
//or
&lt;a href="/example.html" class="fancybox" data-fancybox-type="ajax"&gt;Example&lt;/a&gt;

//Iframe:
&lt;a href="example.html" class="fancybox fancybox.iframe"&gt;Example&lt;/a&gt;

//Inline (will display an element with `id="example"`)
&lt;a href="#example" class="fancybox"&gt;Example&lt;/a&gt;

//SWF:
&lt;a href="example.swf" class="fancybox"&gt;Example&lt;/a&gt;

//Image:
&lt;a href="example.jpg" class="fancybox"&gt;Example&lt;/a&gt;
</code></pre>

<p>Note, ajax requests are subject to the <a href="http://en.wikipedia.org/wiki/Same_origin_policy">same origin policy</a>.
If fancyBox will not be able to get content type, it will try to guess based on 'href' and will quit silently if would not succeed.
(this is different from previsous versions where 'ajax' was used as default type or an error message was displayed).</p>

<h2 id="advanced">Advanced</h2>

<h3 id="helpers">Helpers</h3>

<p>Helpers provide a simple mechanism to extend the capabilities of fancyBox. There are two built-in helpers - 'overlay' and 'title'.
You can disable them, set custom options or enable other helpers. Examples:</p>

<pre><code>//Disable title helper
$(".fancybox").fancybox({
    helpers:  {
        title:  null
    }
});

//Disable overlay helper
$(".fancybox").fancybox({
    helpers:  {
        overlay : null
    }
});

//Change title position and overlay color
$(".fancybox").fancybox({
    helpers:  {
        title : {
            type : 'inside'
        },
        overlay : {
            css : {
                'background' : 'rgba(255,255,255,0.5)'
            }
        }
    }
});

//Enable thumbnail helper and set custom options
$(".fancybox").fancybox({
    helpers:  {
        thumbs : {
            width: 50,
            height: 50
        }
    }
});
</code></pre>

<h3 id="api">API</h3>

<p>Also available are event driven callback methods.  The <code>this</code> keyword refers to the current or upcoming object (depends on callback method). Here is how you can change title:</p>

<pre><code>$(".fancybox").fancybox({
    beforeLoad : function() {
        this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');

        /*
            "this.element" refers to current element, so you can, for example, use the "alt" attribute of the image to store the title:
            this.title = $(this.element).find('img').attr('alt');
        */
    }
});
</code></pre>

<p>It`s possible to open fancyBox programmatically in various ways:</p>

<pre><code>//HTML content:
$.fancybox( '&lt;div&gt;&lt;h1&gt;Lorem Lipsum&lt;/h1&gt;&lt;p&gt;Lorem lipsum&lt;/p&gt;&lt;/div&gt;', {
    title : 'Custom Title'
});

//DOM element:
$.fancybox( $("#inline"), {
    title : 'Custom Title'
});

//Custom object:
$.fancybox({
    href: 'example.jpg',
    title : 'Custom Title'
});

//Array of objects:
$.fancybox([
    {
        href: 'example1.jpg',
        title : 'Custom Title 1'
    },
    {
        href: 'example2.jpg',
        title : 'Custom Title 2'
    }
], {
    padding: 0
});
</code></pre>

<p>There are several methods that allow you to interact with and manipulate fancyBox, example:</p>

<pre><code>//Close fancybox:
$.fancybox.close();
</code></pre>

<p>There is a simply way to access wrapping elements using JS:</p>

<pre><code>$.fancybox.wrap
$.fancybox.skin
$.fancybox.outer
$.fancybox.inner
</code></pre>

<p>You can override CSS to customize the look. For example, make navigation arrows always visible,
change width and move them outside of area (use this snippet after including fancybox.css):</p>

<pre><code>.fancybox-nav span {
    visibility: visible;
}

.fancybox-nav {
    width: 80px;
}

.fancybox-prev {
    left: -80px;
}

.fancybox-next {
    right: -80px;
}
</code></pre>

<p>In that case, you might want to increase space around box:</p>

<pre><code>$(".fancybox").fancybox({
    margin : [20, 60, 20, 60]
});
</code></pre>

<h2 id="bug-tracker">Bug tracker</h2>

<p>Have a bug? Please create an issue on GitHub at https://github.com/fancyapps/fancyBox/issues</p>
