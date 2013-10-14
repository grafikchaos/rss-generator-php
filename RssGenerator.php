<?php

class RssGenerator
{
    public $encoding            = 'UTF-8';
    public $title               = 'YOUR_WEBSITE_TITLE';
    public $subtitle            = 'YOUR_SUBITILE';
    public $language            = 'zh-tw';
    public $description         = 'YOUR_DESCRIPTION';
    public $link                = 'http://example.com/';
    public $ttl                 = 10; // minutes
    public $generator           = 'YOUR_NAME';
    public $version             = '2.0';
    public $rssXmlnsAttributes  = '';

    public function RssGenerator()
    {
    }

    public function set($name, $value)
    {
        $this->$name = $this->sanitize($value);
    }

    /**
     * Make an xml document of the rss stream
     *
     * @param   array   $items  # n row of associative array with theses field:
     * @return  string          # xml document of rss
     */
    public function generateRssXml($items)
    {
        $xml = '';

        // header
        $xml .= "<?xml version=\"1.0\" encoding=\"" . $this->encoding . "\"?>\n";
        $xml .= $this->getRssXmlTag();
        $xml .= "\t<channel>\n";
        // common channel elements shared across all items
        $xml .= $this->getChannelXmlHeaders();
        // loop through the items to create each <item>...</item> node
        $xml .= $this->getItemsXml($items);
        // close the channel tag
        $xml .= "\t</channel>\n";
        // close the rss tag
        $xml .= "</rss>\n";

        return $xml;
    }


    public function getRssXmlTag()
    {
        if (!empty($this->rssXmlnsAttributes)) {
            $escaped_attributes = addslashes($this->rssXmlnsAttributes);
        }
        $xml = "<rss {$escaped_attributes} version=\"{$this->version}\">\n";
        return $xml;
    }

    public function getChannelXmlHeaders()
    {
        $xml = '';
        $xml .= "\t\t<title><![CDATA[" . $this->title . "]]></title>\n";
        $xml .= "\t\t<description><![CDATA[" . $this->description . "]]></description>\n";
        $xml .= "\t\t<link><![CDATA[" . $this->link . "]]></link>\n";
        $xml .= "\t\t<language>" . $this->language . "</language>\n";
        $xml .= "\t\t<lastBuildDate>" . date(DATE_RSS) . "</lastBuildDate>\n";
        $xml .= "\t\t<ttl>" . $this->ttl . "</ttl>\n";
        $xml .= "\t\t<generator><![CDATA[" . $this->generator . "]]></generator>\n";

        return $xml;
    }

    public function getItemsXml(array $items = array())
    {
        $xml = '';
        foreach ($items as $item) {
            $xml .= "\t\t<item>\n";
            foreach ($item as $key => $val) {
                switch($key) {
                    case 'title':
                        $xml .= "\t\t\t<title><![CDATA[" . $this->sanitize($val) . "]]></title>\n";
                        break;

                    case 'description':
                        $xml .= "\t\t\t<description><![CDATA[" . $this->sanitize($val) . "]]></description>\n";
                        break;

                    case 'link':
                        if (!empty($val))
                            $xml .= "\t\t\t<link><![CDATA[" . $this->sanitize($val) . "]]></link>\n";
                        break;

                    case 'pubDate':
                        if (!empty($val))
                            $xml .= "\t\t\t<pubDate>" . date(DATE_RSS, strtotime($val)) . "</pubDate>\n";
                        break;

                    default:
                        $xml .= "\t\t\t<$key><![CDATA[" . $this->sanitize($val) . "]]></$key>\n";
                        break;
                }
            }
            $xml .= "\t\t</item>\n";
        }

        return $xml;
    }

    public function sanitize($val)
    {
        return stripslashes($val);
    }

}
