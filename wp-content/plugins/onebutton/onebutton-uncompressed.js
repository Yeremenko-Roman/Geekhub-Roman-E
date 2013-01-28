function OneButton(item, url, title)
{
    url = encodeURIComponent(url);
    title = encodeURIComponent(title);
    var sp = document.getElementById('sharepage');
    if (null != sp) {
        var links = sp.getElementsByTagName('a');
        for (var i=0; i<links.length; ++i) {
            var link = links[i];
            var href = (link.oldhref) ? link.oldhref : link.href;
            if (!link.oldhref) {
                link.oldhref = href;
            }

            link.href = href.replace(/\{url\}/g, url).replace(/\{title\}/g, title).replace(/\{description\}/g, '').replace(/\{tags\}/g, '');
        }

        if (sp.parentNode != item) {
            item.appendChild(sp.parentNode.removeChild(sp));
		}

        sp.style.display = 'block';
    }
}

function CloseOneButton()
{
    var sp = document.getElementById('sharepage');
    if (null != sp) {
        sp.style.display = 'none';
    }
}
