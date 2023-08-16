<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Sitemap
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Sitemap\Preference\Sitemap\Model;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * {@inheritdoc}
     */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null, $images = null)
    {
        $url = $this->_getUrl($url);
        $row = '<loc>' . $this->_escaper->escapeUrl($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->_getFormattedLastmodDate($lastmod) . '</lastmod>';
        }
        if ($changefreq) {
            $row .= '<changefreq>' . $this->_escaper->escapeHtml($changefreq) . '</changefreq>';
        }
        if ($priority) {
            $row .= sprintf('<priority>%.1f</priority>', $this->_escaper->escapeHtml($priority));
        }
        if ($images) {
            // Add Images to sitemap
            foreach ($images->getCollection() as $image) {
                if ($image->getUrl() || $images->getTitle() || $image->getCaption()) {
                    $row .= '<image:image>';
                    if ($image->getUrl()) {
                        $row .= '<image:loc>' . $this->_escaper->escapeUrl($image->getUrl()) . '</image:loc>';
                    }
                    if ($images->getTitle()) {
                        $row .= '<image:title>' . $this->escapeXmlText($images->getTitle()) . '</image:title>';
                    }
                    if ($image->getCaption()) {
                        $row .= '<image:caption>' . $this->escapeXmlText($image->getCaption()) . '</image:caption>';
                    }
                    $row .= '</image:image>';
                }
            }
            // Add PageMap image for Google web search
            if($images->getTitle() || $images->getThumbnail()){
                $row .= '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0"><DataObject type="thumbnail">';
                if($images->getTitle()){
                    $row .= '<Attribute name="name" value="' . $this->_escaper->escapeHtmlAttr($images->getTitle()) . '"/>';
                }
                if($images->getThumbnail()){
                    $row .= '<Attribute name="src" value="' . $this->_escaper->escapeUrl($images->getThumbnail()) . '"/>';
                }
                $row .= '</DataObject></PageMap>';
            }

        }

        return '<url>' . $row . '</url>';
    }

    /**
     * Escape string for XML context.
     *
     * @param string $text
     * @return string
     */
    private function escapeXmlText(string $text): string
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $fragment = $doc->createDocumentFragment();
        $fragment->appendChild($doc->createTextNode($text));
        return $doc->saveXML($fragment);
    }
}
