/* global ActiveXObject */

/*! Javascript Generic DOM
 * By Eric Stevens
 */
function fetchDOM (filename) // eslint-disable-line no-unused-vars
{
    var xmldom
    if (document.implementation && document.implementation.createDocument)
    {
        // Mozilla style browsers
        xmldom = document.implementation.createDocument('', '', null)
    }
    else if (window.ActiveXObject)
    {
        // IE style browsers
        xmldom = new ActiveXObject('Microsoft.XMLDOM')
    }

    xmldom.async = false
    try
    {
        xmldom.load(filename)
    }
    catch (e)
    {
        xmldom.parseXML('<strong>Failed to load ' + filename + '</strong>')
    }
    return xmldom
}

var dom = '' // eslint-disable-line no-unused-vars

if (document.implementation && document.implementation.createDocument)
{
    dom = document.implementation.createDocument('', '', null)
}
else if (window.ActiveXObject)
{
    dom = new ActiveXObject('Microsoft.XMLDOM')
}

function fetchDOMasync (filename, args, theCode) // eslint-disable-line no-unused-vars
{
    var xmldom
    try
    {
        xmldom = new ActiveXObject('Msxml2.XMLHTTP')
    }
    catch (e)
    {
        try
        {
            xmldom = new ActiveXObject('Microsoft.XMLHTTP')
        }
        catch (E)
        {
            xmldom = false
        }
    }
    if (!xmldom && typeof XMLHttpRequest !== 'undefined')
    {
        xmldom = new XMLHttpRequest()
    }
    xmldom.onreadystatechange = function ()
    {
        if (xmldom.readyState === 4)
        {
            theCode()
        }
    }
    xmldom.open('POST', filename, true)
    xmldom.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8')
    xmldom.send(args)

    return xmldom
}

function createXML (node) // eslint-disable-line no-unused-vars
{
    if (!node) { return '<strong>You cannot pass null to createXML</strong>' }
    if (node.xml) { return node.xml }

    var out = ''
    if (node.nodeType === 1)
    {
        var x = 0
        out = '<' + node.nodeName
        for (x = 0; x < node.attributes.length; x++)
        {
            out = out + ' ' + node.attributes[x].name + '="' + HTMLencode(node.attributes[x].nodeValue) + '"'
        }
        out = out + '>'
        for (x = 0; x < node.childNodes.length; x++)
        {
            out = out + createXML(node.childNodes[x])
        }
        out = out + '</' + node.nodeName + '>'
    }
    else if (node.nodeType === 3)
    {
        out = out + HTMLencode(node.nodeValue)
    }

    return out
}

function selectSingleNode (node, name)
{
    var nextName = ''
    if (name.indexOf('/') > 0)
    {
        nextName = name.substring(name.indexOf('/') + 1)
        name = name.substring(0, name.indexOf('/'))
    }
    for (var x = 0; x < node.childNodes.length; x++)
    {
        if (node.childNodes[x].nodeName === name)
        {
            if (nextName === '')
            {
                return node.childNodes[x]
            }
            else
            {
                return selectSingleNode(node.childNodes[x], nextName)
            }
        }
    }
}

function nodeText (node)
{
    var out = ''
    for (var y = 0; y < node.childNodes.length; y++)
    {
        if (node.childNodes[y].nodeType === 3)
        {
            out += node.childNodes[y].nodeValue
        }
        else if (node.childNodes[y].nodeType === 1)
        {
            out += nodeText(node.childNodes[y])
        }
    }
    return out
}

function parseRSS (xml) // eslint-disable-line no-unused-vars
{
    var rss = selectSingleNode(xml, 'rss')
    var channel = selectSingleNode(rss, 'channel')

    var feed = []
    // collect rss headers
    feed['title'] = HTMLencode(nodeText(selectSingleNode(channel, 'title')))
    feed['link'] = HTMLencode(nodeText(selectSingleNode(channel, 'link')))
    feed['description'] = HTMLencode(nodeText(selectSingleNode(channel, 'description')))
    var image = selectSingleNode(channel, 'image')
    feed['image'] = []
    feed['image']['title'] = HTMLencode(nodeText(selectSingleNode(image, 'title')))
    feed['image']['url'] = HTMLencode(nodeText(selectSingleNode(image, 'url')))
    feed['image']['link'] = HTMLencode(nodeText(selectSingleNode(image, 'link')))
    feed['items'] = []
    // collect rss items
    var node
    var y = 0
    for (var x = 0; x < channel.childNodes.length; x++)
    {
        node = channel.childNodes[x]
        if (node.nodeType === 1)
        { // standard element
            if (node.nodeName === 'item')
            {
                feed['items'][y] = []
                feed['items'][y]['title'] = HTMLencode(nodeText(selectSingleNode(node, 'title')))
                feed['items'][y]['link'] = HTMLencode(nodeText(selectSingleNode(node, 'link')))
                feed['items'][y]['description'] = HTMLencode(nodeText(selectSingleNode(node, 'description')))
                feed['items'][y]['pubdate'] = HTMLencode(nodeText(selectSingleNode(node, 'pubDate')))
                y = y + 1
            }
        }
    }
    return feed
}

function HTMLencode (input)
{
    if (input == null)
    {
        return ''
    }
    else
    {
        return input.replace(/&/g, '&amp;').replace(/'/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }
}

function HTMLdecode (input) // eslint-disable-line no-unused-vars
{
    if (input == null)
    {
        return ''
    }
    else
    {
        return input.replace(/&gt;/g, '>').replace(/&lt;/g, '<').replace(/&quot;/g, "'").replace(/&amp;/g, '&')
    }
}
