<?php

/**
 * Parsedown
 * http://parsedown.org
 *
 * @category CTCD
 * @package  CTCD_Core
 *
 * @author   Emanuil Rusev <http://erusev.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

/**
 * Markdown to HTML converter
 */
class Parsedown
{
    const version = '1.8.0-beta-7';

    private static $instances = array();

    protected $definitionData;

    protected $breaksEnabled;

    protected $markupEscaped;

    protected $urlsLinked = true;

    protected $safeMode;

    protected $strictMode;

    protected $safeLinksWhitelist = array(
        'http://',
        'https://',
        'ftp://',
        'ftps://',
        'mailto:',
        'tel:',
        'data:image/png;base64,',
        'data:image/gif;base64,',
        'data:image/jpeg;base64,',
        'irc:',
        'ircs:',
        'git:',
        'ssh:',
        'news:',
        'steam:',
    );

    protected $blockTypes = array(
        '#' => array('Header'),
        '*' => array('Rule', 'List'),
        '+' => array('List'),
        '-' => array('SetextHeader', 'Table', 'Rule', 'List'),
        '0' => array('List'),
        '1' => array('List'),
        '2' => array('List'),
        '3' => array('List'),
        '4' => array('List'),
        '5' => array('List'),
        '6' => array('List'),
        '7' => array('List'),
        '8' => array('List'),
        '9' => array('List'),
        ':' => array('Table'),
        '<' => array('Comment', 'Markup'),
        '=' => array('SetextHeader'),
        '>' => array('Quote'),
        '[' => array('Reference'),
        '_' => array('Rule'),
        '`' => array('FencedCode'),
        '|' => array('Table'),
        '~' => array('FencedCode'),
    );

    protected $unmarkedBlockTypes = array('Code');

    protected $inlineTypes = array(
        '!' => array('Image'),
        '&' => array('SpecialCharacter'),
        '*' => array('Emphasis'),
        ':' => array('Url'),
        '<' => array('UrlTag', 'EmailTag', 'Markup'),
        '[' => array('Link'),
        '_' => array('Emphasis'),
        '`' => array('Code'),
        '~' => array('Strikethrough'),
        '\\' => array('EscapeSequence'),
    );

    protected $inlineMarkerList = '!*_&[:<`~\\';

    protected $specialCharacters = array(
        '\\', '`', '*', '_', '{', '}', '[', ']', '(', ')', '>', '#', '+', '-', '.', '!', '|', '~'
    );

    protected $strongRegex = array(
        '*' => '/^[*]{2}((?:\\\\\*|[^*]|[*][^*]*+[*])+?)[*]{2}(?![*])/s',
        '_' => '/^__((?:\\\\_|[^_]|_[^_]*+_)+?)__(?!_)/us',
    );

    protected $emRegex = array(
        '*' => '/^[*]((?:\\\\\*|[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/s',
        '_' => '/^_((?:\\\\_|[^_]|__[^_]*__)+?)_(?!_)\b/us',
    );

    protected $regexHtmlAttribute = '[a-zA-Z_:][\w:.-]*+(?:\s*+=\s*+(?:[^"\'=<>`\s]+|"[^"]*+"|\'[^\']*+\'))?+';

    protected $voidElements = array(
        'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source',
    );

    protected $textLevelElements = array(
        'a', 'br', 'bdo', 'abbr', 'blink', 'nextid', 'acronym', 'basefont',
        'b', 'em', 'big', 'cite', 'small', 'spacer', 'listing',
        'i', 'rp', 'del', 'code',          'strike', 'marquee',
        'q', 'rt', 'ins', 'font',          'strong',
        's', 'tt', 'kbd', 'mark',
        'u', 'xm', 'sub', 'nobr',
        'sup', 'ruby',
        'var', 'span',
        'wbr', 'time',
    );

    public function text($text)
    {
        $elements = $this->textElements($text);

        # convert to markup
        $markup = $this->elements($elements);

        # trim line breaks
        $markup = trim($markup, "\n");

        return $markup;
    }

    public function setBreaksEnabled($breaksEnabled)
    {
        $this->breaksEnabled = $breaksEnabled;

        return $this;
    }

    public function setMarkupEscaped($markupEscaped)
    {
        $this->markupEscaped = $markupEscaped;

        return $this;
    }

    public function setUrlsLinked($urlsLinked)
    {
        $this->urlsLinked = $urlsLinked;

        return $this;
    }

    public function setSafeMode($safeMode)
    {
        $this->safeMode = (bool) $safeMode;

        return $this;
    }

    public function setStrictMode($strictMode)
    {
        $this->strictMode = (bool) $strictMode;

        return $this;
    }

    public function line($text, $nonNestables = array())
    {
        return $this->elements($this->lineElements($text, $nonNestables));
    }

    protected function textElements($text)
    {
        # make sure no definitions are set
        $this->definitionData = array();

        # standardize line breaks
        $text = str_replace(array("\r\n", "\r"), "\n", $text);

        # remove surrounding line breaks
        $text = trim($text, "\n");

        # split text into lines
        $lines = explode("\n", $text);

        # iterate through lines to identify blocks
        return $this->linesElements($lines);
    }

    protected function lines(array $lines)
    {
        return $this->elements($this->linesElements($lines));
    }

    protected function linesElements(array $lines)
    {
        $elements = array();
        $currentBlock = null;

        foreach ($lines as $line)
        {
            if (chop($line) === '')
            {
                if (isset($currentBlock))
                {
                    $currentBlock['interrupted'] = (isset($currentBlock['interrupted'])
                        ? $currentBlock['interrupted'] + 1 : 1
                    );
                }

                continue;
            }

            while (($beforeTab = strstr($line, "\t", true)) !== false)
            {
                $shortage = 4 - mb_strlen($beforeTab, 'utf-8') % 4;

                $line = $beforeTab
                    . str_repeat(' ', $shortage)
                    . substr($line, strlen($beforeTab) + 1)
                ;
            }

            $indent = strspn($line, ' ');

            $text = $indent > 0 ? substr($line, $indent) : $line;

            # ~

            $Line = array('body' => $line, 'indent' => $indent, 'text' => $text);

            # ~

            if (isset($currentBlock['continuable']))
            {
                $methodName = 'block' . $currentBlock['type'] . 'Continue';
                $block = $this->$methodName($Line, $currentBlock);

                if (isset($block))
                {
                    $currentBlock = $block;

                    continue;
                }
                else
                {
                    if ($this->isBlockCompletable($currentBlock['type']))
                    {
                        $methodName = 'block' . $currentBlock['type'] . 'Complete';
                        $currentBlock = $this->$methodName($currentBlock);
                    }
                }
            }

            # ~

            $marker = $text[0];

            # ~

            $blockTypes = $this->unmarkedBlockTypes;

            if (isset($this->blockTypes[$marker]))
            {
                foreach ($this->blockTypes[$marker] as $blockType)
                {
                    $blockTypes []= $blockType;
                }
            }

            #
            # ~

            foreach ($blockTypes as $blockType)
            {
                $block = $this->{"block$blockType"}($Line, $currentBlock);

                if (isset($block))
                {
                    $block['type'] = $blockType;

                    if ( ! isset($block['identified']))
                    {
                        if (isset($currentBlock))
                        {
                            $elements[] = $this->extractElement($currentBlock);
                        }

                        $block['identified'] = true;
                    }

                    if ($this->isBlockContinuable($blockType))
                    {
                        $block['continuable'] = true;
                    }

                    $currentBlock = $block;

                    continue 2;
                }
            }

            # ~

            if (isset($currentBlock) and $currentBlock['type'] === 'Paragraph')
            {
                $block = $this->paragraphContinue($Line, $currentBlock);
            }

            if (isset($block))
            {
                $currentBlock = $block;
            }
            else
            {
                if (isset($currentBlock))
                {
                    $elements[] = $this->extractElement($currentBlock);
                }

                $currentBlock = $this->paragraph($Line);

                $currentBlock['identified'] = true;
            }
        }

        # ~

        if (isset($currentBlock['continuable']) and $this->isBlockCompletable($currentBlock['type']))
        {
            $methodName = 'block' . $currentBlock['type'] . 'Complete';
            $currentBlock = $this->$methodName($currentBlock);
        }

        # ~

        if (isset($currentBlock))
        {
            $elements[] = $this->extractElement($currentBlock);
        }

        # ~

        return $elements;
    }

    protected function extractElement(array $component)
    {
        if ( ! isset($component['element']))
        {
            if (isset($component['markup']))
            {
                $component['element'] = array('rawHtml' => $component['markup']);
            }
            elseif (isset($component['hidden']))
            {
                $component['element'] = array();
            }
        }

        return $component['element'];
    }

    protected function isBlockContinuable($Type)
    {
        return method_exists($this, 'block' . $Type . 'Continue');
    }

    protected function isBlockCompletable($Type)
    {
        return method_exists($this, 'block' . $Type . 'Complete');
    }

    protected function blockCode($Line, $block = null)
    {
        if (isset($block) and $block['type'] === 'Paragraph' and ! isset($block['interrupted']))
        {
            return;
        }

        if ($Line['indent'] >= 4)
        {
            $text = substr($Line['body'], 4);

            $block = array(
                'element' => array(
                    'name' => 'pre',
                    'element' => array(
                        'name' => 'code',
                        'text' => $text,
                    ),
                ),
            );

            return $block;
        }
    }

    protected function blockCodeContinue($Line, $block)
    {
        if ($Line['indent'] >= 4)
        {
            if (isset($block['interrupted']))
            {
                $block['element']['element']['text'] .= str_repeat("\n", $block['interrupted']);

                unset($block['interrupted']);
            }

            $block['element']['element']['text'] .= "\n";

            $text = substr($Line['body'], 4);

            $block['element']['element']['text'] .= $text;

            return $block;
        }
    }

    protected function blockCodeComplete($block)
    {
        return $block;
    }

    protected function blockComment($Line)
    {
        if ($this->markupEscaped or $this->safeMode)
        {
            return;
        }

        if (strpos($Line['text'], '<!--') === 0)
        {
            $block = array(
                'element' => array(
                    'rawHtml' => $Line['body'],
                    'autobreak' => true,
                ),
            );

            if (strpos($Line['text'], '-->') !== false)
            {
                $block['closed'] = true;
            }

            return $block;
        }
    }

    protected function blockCommentContinue($Line, array $block)
    {
        if (isset($block['closed']))
        {
            return;
        }

        $block['element']['rawHtml'] .= "\n" . $Line['body'];

        if (strpos($Line['text'], '-->') !== false)
        {
            $block['closed'] = true;
        }

        return $block;
    }

    protected function blockFencedCode($Line)
    {
        $marker = $Line['text'][0];

        $openerLength = strspn($Line['text'], $marker);

        if ($openerLength < 3)
        {
            return;
        }

        $infostring = trim(substr($Line['text'], $openerLength), "\t ");

        if (strpos($infostring, '`') !== false)
        {
            return;
        }

        $element = array(
            'name' => 'code',
            'text' => '',
        );

        if ($infostring !== '')
        {
            /**
             * https://www.w3.org/TR/2011/WD-html5-20110525/elements.html#classes
             * Every HTML element may have a class attribute specified.
             * The attribute, if specified, must have a value that is a set
             * of space-separated tokens representing the various classes
             * that the element belongs to.
             * [...]
             * The space characters, for the purposes of this specification,
             * are U+0020 SPACE, U+0009 CHARACTER TABULATION (tab),
             * U+000A LINE FEED (LF), U+000C FORM FEED (FF), and
             * U+000D CARRIAGE RETURN (CR).
             */
            $language = substr($infostring, 0, strcspn($infostring, " \t\n\f\r"));

            $element['attributes'] = array('class' => "language-$language");
        }

        $block = array(
            'char' => $marker,
            'openerLength' => $openerLength,
            'element' => array(
                'name' => 'pre',
                'element' => $element,
            ),
        );

        return $block;
    }

    protected function blockFencedCodeContinue($Line, $block)
    {
        if (isset($block['complete']))
        {
            return;
        }

        if (isset($block['interrupted']))
        {
            $block['element']['element']['text'] .= str_repeat("\n", $block['interrupted']);

            unset($block['interrupted']);
        }

        if (($len = strspn($Line['text'], $block['char'])) >= $block['openerLength']
            and chop(substr($Line['text'], $len), ' ') === ''
        ) {
            $block['element']['element']['text'] = substr($block['element']['element']['text'], 1);

            $block['complete'] = true;

            return $block;
        }

        $block['element']['element']['text'] .= "\n" . $Line['body'];

        return $block;
    }

    protected function blockFencedCodeComplete($block)
    {
        return $block;
    }

    protected function blockHeader($Line)
    {
        $level = strspn($Line['text'], '#');

        if ($level > 6)
        {
            return;
        }

        $text = trim($Line['text'], '#');

        if ($this->strictMode and isset($text[0]) and $text[0] !== ' ')
        {
            return;
        }

        $text = trim($text, ' ');

        $block = array(
            'element' => array(
                'name' => 'h' . $level,
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $text,
                    'destination' => 'elements',
                )
            ),
        );

        return $block;
    }

    protected function blockList($Line, array $currentBlock = null)
    {
        list($name, $pattern) = $Line['text'][0] <= '-' ? array('ul', '[*+-]') : array('ol', '[0-9]{1,9}+[.\)]');

        if (preg_match('/^('.$pattern.'([ ]++|$))(.*+)/', $Line['text'], $matches))
        {
            $contentIndent = strlen($matches[2]);

            if ($contentIndent >= 5)
            {
                $contentIndent -= 1;
                $matches[1] = substr($matches[1], 0, -$contentIndent);
                $matches[3] = str_repeat(' ', $contentIndent) . $matches[3];
            }
            elseif ($contentIndent === 0)
            {
                $matches[1] .= ' ';
            }

            $markerWithoutWhitespace = strstr($matches[1], ' ', true);

            $block = array(
                'indent' => $Line['indent'],
                'pattern' => $pattern,
                'data' => array(
                    'type' => $name,
                    'marker' => $matches[1],
                    'markerType' => ($name === 'ul' ? $markerWithoutWhitespace : substr($markerWithoutWhitespace, -1)),
                ),
                'element' => array(
                    'name' => $name,
                    'elements' => array(),
                ),
            );
            $block['data']['markerTypeRegex'] = preg_quote($block['data']['markerType'], '/');

            if ($name === 'ol')
            {
                $listStart = ltrim(strstr($matches[1], $block['data']['markerType'], true), '0') ?: '0';

                if ($listStart !== '1')
                {
                    if (
                        isset($currentBlock)
                        and $currentBlock['type'] === 'Paragraph'
                        and ! isset($currentBlock['interrupted'])
                    ) {
                        return;
                    }

                    $block['element']['attributes'] = array('start' => $listStart);
                }
            }

            $block['li'] = array(
                'name' => 'li',
                'handler' => array(
                    'function' => 'li',
                    'argument' => !empty($matches[3]) ? array($matches[3]) : array(),
                    'destination' => 'elements'
                )
            );

            $block['element']['elements'] []= & $block['li'];

            return $block;
        }
    }

    protected function blockListContinue($Line, array $block)
    {
        if (isset($block['interrupted']) and empty($block['li']['handler']['argument']))
        {
            return null;
        }

        $requiredIndent = ($block['indent'] + strlen($block['data']['marker']));

        if ($Line['indent'] < $requiredIndent
            and (
                (
                    $block['data']['type'] === 'ol'
                    and preg_match('/^[0-9]++'.$block['data']['markerTypeRegex'].'(?:[ ]++(.*)|$)/', $Line['text'], $matches)
                ) or (
                    $block['data']['type'] === 'ul'
                    and preg_match('/^'.$block['data']['markerTypeRegex'].'(?:[ ]++(.*)|$)/', $Line['text'], $matches)
                )
            )
        ) {
            if (isset($block['interrupted']))
            {
                $block['li']['handler']['argument'] []= '';

                $block['loose'] = true;

                unset($block['interrupted']);
            }

            unset($block['li']);

            $text = isset($matches[1]) ? $matches[1] : '';

            $block['indent'] = $Line['indent'];

            $block['li'] = array(
                'name' => 'li',
                'handler' => array(
                    'function' => 'li',
                    'argument' => array($text),
                    'destination' => 'elements'
                )
            );

            $block['element']['elements'] []= & $block['li'];

            return $block;
        }
        elseif ($Line['indent'] < $requiredIndent and $this->blockList($Line))
        {
            return null;
        }

        if ($Line['text'][0] === '[' and $this->blockReference($Line))
        {
            return $block;
        }

        if ($Line['indent'] >= $requiredIndent)
        {
            if (isset($block['interrupted']))
            {
                $block['li']['handler']['argument'] []= '';

                $block['loose'] = true;

                unset($block['interrupted']);
            }

            $text = substr($Line['body'], $requiredIndent);

            $block['li']['handler']['argument'] []= $text;

            return $block;
        }

        if ( ! isset($block['interrupted']))
        {
            $text = preg_replace('/^[ ]{0,'.$requiredIndent.'}+/', '', $Line['body']);

            $block['li']['handler']['argument'] []= $text;

            return $block;
        }
    }

    protected function blockListComplete(array $block)
    {
        if (isset($block['loose']))
        {
            foreach ($block['element']['elements'] as &$li)
            {
                if (end($li['handler']['argument']) !== '')
                {
                    $li['handler']['argument'] []= '';
                }
            }
        }

        return $block;
    }

    protected function blockQuote($Line)
    {
        if (preg_match('/^>[ ]?+(.*+)/', $Line['text'], $matches))
        {
            $block = array(
                'element' => array(
                    'name' => 'blockquote',
                    'handler' => array(
                        'function' => 'linesElements',
                        'argument' => (array) $matches[1],
                        'destination' => 'elements',
                    )
                ),
            );

            return $block;
        }
    }

    protected function blockQuoteContinue($Line, array $block)
    {
        if (isset($block['interrupted']))
        {
            return;
        }

        if ($Line['text'][0] === '>' and preg_match('/^>[ ]?+(.*+)/', $Line['text'], $matches))
        {
            $block['element']['handler']['argument'] []= $matches[1];

            return $block;
        }

        if ( ! isset($block['interrupted']))
        {
            $block['element']['handler']['argument'] []= $Line['text'];

            return $block;
        }
    }

    protected function blockRule($Line)
    {
        $marker = $Line['text'][0];

        if (substr_count($Line['text'], $marker) >= 3 and chop($Line['text'], " $marker") === '')
        {
            $block = array(
                'element' => array(
                    'name' => 'hr',
                ),
            );

            return $block;
        }
    }

    protected function blockSetextHeader($Line, array $block = null)
    {
        if ( ! isset($block) or $block['type'] !== 'Paragraph' or isset($block['interrupted']))
        {
            return;
        }

        if ($Line['indent'] < 4 and chop(chop($Line['text'], ' '), $Line['text'][0]) === '')
        {
            $block['element']['name'] = $Line['text'][0] === '=' ? 'h1' : 'h2';

            return $block;
        }
    }

    protected function blockMarkup($Line)
    {
        if ($this->markupEscaped or $this->safeMode)
        {
            return;
        }

        if (preg_match('/^<[\/]?+(\w*)(?:[ ]*+'.$this->regexHtmlAttribute.')*+[ ]*+(\/)?>/', $Line['text'], $matches))
        {
            $element = strtolower($matches[1]);

            if (in_array($element, $this->textLevelElements))
            {
                return;
            }

            $block = array(
                'name' => $matches[1],
                'element' => array(
                    'rawHtml' => $Line['text'],
                    'autobreak' => true,
                ),
            );

            return $block;
        }
    }

    protected function blockMarkupContinue($Line, array $block)
    {
        if (isset($block['closed']) or isset($block['interrupted']))
        {
            return;
        }

        $block['element']['rawHtml'] .= "\n" . $Line['body'];

        return $block;
    }

    protected function blockReference($Line)
    {
        if (strpos($Line['text'], ']') !== false
            and preg_match('/^\[(.+?)\]:[ ]*+<?(\S+?)>?(?:[ ]+["\'(](.+)["\')])?[ ]*+$/', $Line['text'], $matches)
        ) {
            $id = strtolower($matches[1]);

            $Data = array(
                'url' => $matches[2],
                'title' => isset($matches[3]) ? $matches[3] : null,
            );

            $this->definitionData['Reference'][$id] = $Data;

            $block = array(
                'element' => array(),
            );

            return $block;
        }
    }

    protected function blockTable($Line, array $block = null)
    {
        if ( ! isset($block) or $block['type'] !== 'Paragraph' or isset($block['interrupted']))
        {
            return;
        }

        if (
            strpos($block['element']['handler']['argument'], '|') === false
            and strpos($Line['text'], '|') === false
            and strpos($Line['text'], ':') === false
            or strpos($block['element']['handler']['argument'], "\n") !== false
        ) {
            return;
        }

        if (chop($Line['text'], ' -:|') !== '')
        {
            return;
        }

        $alignments = array();

        $divider = $Line['text'];

        $divider = trim($divider);
        $divider = trim($divider, '|');

        $dividerCells = explode('|', $divider);

        foreach ($dividerCells as $dividerCell)
        {
            $dividerCell = trim($dividerCell);

            if ($dividerCell === '')
            {
                return;
            }

            $alignment = null;

            if ($dividerCell[0] === ':')
            {
                $alignment = 'left';
            }

            if (substr($dividerCell, - 1) === ':')
            {
                $alignment = $alignment === 'left' ? 'center' : 'right';
            }

            $alignments []= $alignment;
        }

        # ~

        $headerElements = array();

        $header = $block['element']['handler']['argument'];

        $header = trim($header);
        $header = trim($header, '|');

        $headerCells = explode('|', $header);

        if (count($headerCells) !== count($alignments))
        {
            return;
        }

        foreach ($headerCells as $index => $headerCell)
        {
            $headerCell = trim($headerCell);

            $headerElement = array(
                'name' => 'th',
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $headerCell,
                    'destination' => 'elements',
                )
            );

            if (isset($alignments[$index]))
            {
                $alignment = $alignments[$index];

                $headerElement['attributes'] = array(
                    'style' => "text-align: $alignment;",
                );
            }

            $headerElements []= $headerElement;
        }

        # ~

        $block = array(
            'alignments' => $alignments,
            'identified' => true,
            'element' => array(
                'name' => 'table',
                'elements' => array(),
            ),
        );

        $block['element']['elements'] []= array(
            'name' => 'thead',
        );

        $block['element']['elements'] []= array(
            'name' => 'tbody',
            'elements' => array(),
        );

        $block['element']['elements'][0]['elements'] []= array(
            'name' => 'tr',
            'elements' => $headerElements,
        );

        return $block;
    }

    protected function blockTableContinue($Line, array $block)
    {
        if (isset($block['interrupted']))
        {
            return;
        }

        if (count($block['alignments']) === 1 or $Line['text'][0] === '|' or strpos($Line['text'], '|'))
        {
            $elements = array();

            $row = $Line['text'];

            $row = trim($row);
            $row = trim($row, '|');

            preg_match_all('/(?:(\\\\[|])|[^|`]|`[^`]++`|`)++/', $row, $matches);

            $cells = array_slice($matches[0], 0, count($block['alignments']));

            foreach ($cells as $index => $cell)
            {
                $cell = trim($cell);

                $element = array(
                    'name' => 'td',
                    'handler' => array(
                        'function' => 'lineElements',
                        'argument' => $cell,
                        'destination' => 'elements',
                    )
                );

                if (isset($block['alignments'][$index]))
                {
                    $element['attributes'] = array(
                        'style' => 'text-align: ' . $block['alignments'][$index] . ';',
                    );
                }

                $elements []= $element;
            }

            $element = array(
                'name' => 'tr',
                'elements' => $elements,
            );

            $block['element']['elements'][1]['elements'] []= $element;

            return $block;
        }
    }

    protected function paragraph($Line)
    {
        return array(
            'type' => 'Paragraph',
            'element' => array(
                'name' => 'p',
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $Line['text'],
                    'destination' => 'elements',
                ),
            ),
        );
    }

    protected function paragraphContinue($Line, array $block)
    {
        if (isset($block['interrupted']))
        {
            return;
        }

        $block['element']['handler']['argument'] .= "\n".$Line['text'];

        return $block;
    }

    protected function lineElements($text, $nonNestables = array())
    {
        # standardize line breaks
        $text = str_replace(array("\r\n", "\r"), "\n", $text);

        $elements = array();

        $nonNestables = (empty($nonNestables)
            ? array()
            : array_combine($nonNestables, $nonNestables)
        );

        # $excerpt is based on the first occurrence of a marker

        while ($excerpt = strpbrk($text, $this->inlineMarkerList))
        {
            $marker = $excerpt[0];

            $markerPosition = strlen($text) - strlen($excerpt);

            $Excerpt = array('text' => $excerpt, 'context' => $text);

            foreach ($this->inlineTypes[$marker] as $inlineType)
            {
                # check to see if the current inline type is nestable in the current context

                if (isset($nonNestables[$inlineType]))
                {
                    continue;
                }

                $inline = $this->{"inline$inlineType"}($Excerpt);

                if ( ! isset($inline))
                {
                    continue;
                }

                # makes sure that the inline belongs to "our" marker

                if (isset($inline['position']) and $inline['position'] > $markerPosition)
                {
                    continue;
                }

                # sets a default inline position

                if ( ! isset($inline['position']))
                {
                    $inline['position'] = $markerPosition;
                }

                # cause the new element to 'inherit' our non nestables


                $inline['element']['nonNestables'] = isset($inline['element']['nonNestables'])
                    ? array_merge($inline['element']['nonNestables'], $nonNestables)
                    : $nonNestables
                ;

                # the text that comes before the inline
                $unmarkedText = substr($text, 0, $inline['position']);

                # compile the unmarked text
                $inlineText = $this->inlineText($unmarkedText);
                $elements[] = $inlineText['element'];

                # compile the inline
                $elements[] = $this->extractElement($inline);

                # remove the examined text
                $text = substr($text, $inline['position'] + $inline['extent']);

                continue 2;
            }

            # the marker does not belong to an inline

            $unmarkedText = substr($text, 0, $markerPosition + 1);

            $inlineText = $this->inlineText($unmarkedText);
            $elements[] = $inlineText['element'];

            $text = substr($text, $markerPosition + 1);
        }

        $inlineText = $this->inlineText($text);
        $elements[] = $inlineText['element'];

        foreach ($elements as &$element)
        {
            if ( ! isset($element['autobreak']))
            {
                $element['autobreak'] = false;
            }
        }

        return $elements;
    }

    protected function inlineText($text)
    {
        $inline = array(
            'extent' => strlen($text),
            'element' => array(),
        );

        $inline['element']['elements'] = self::pregReplaceElements(
            $this->breaksEnabled ? '/[ ]*+\n/' : '/(?:[ ]*+\\\\|[ ]{2,}+)\n/',
            array(
                array('name' => 'br'),
                array('text' => "\n"),
            ),
            $text
        );

        return $inline;
    }

    protected function inlineCode($Excerpt)
    {
        $marker = $Excerpt['text'][0];

        if (preg_match('/^(['.$marker.']++)[ ]*+(.+?)[ ]*+(?<!['.$marker.'])\1(?!'.$marker.')/s', $Excerpt['text'], $matches))
        {
            $text = $matches[2];
            $text = preg_replace('/[ ]*+\n/', ' ', $text);

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'code',
                    'text' => $text,
                ),
            );
        }
    }

    protected function inlineEmailTag($Excerpt)
    {
        $hostnameLabel = '[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?';

        $commonMarkEmail = '[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]++@'
            . $hostnameLabel . '(?:\.' . $hostnameLabel . ')*';

        if (strpos($Excerpt['text'], '>') !== false
            and preg_match("/^<((mailto:)?$commonMarkEmail)>/i", $Excerpt['text'], $matches)
        ){
            $url = $matches[1];

            if ( ! isset($matches[2]))
            {
                $url = "mailto:$url";
            }

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'a',
                    'text' => $matches[1],
                    'attributes' => array(
                        'href' => $url,
                    ),
                ),
            );
        }
    }

    protected function inlineEmphasis($Excerpt)
    {
        if ( ! isset($Excerpt['text'][1]))
        {
            return;
        }

        $marker = $Excerpt['text'][0];

        if ($Excerpt['text'][1] === $marker and preg_match($this->strongRegex[$marker], $Excerpt['text'], $matches))
        {
            $emphasis = 'strong';
        }
        elseif (preg_match($this->emRegex[$marker], $Excerpt['text'], $matches))
        {
            $emphasis = 'em';
        }
        else
        {
            return;
        }

        return array(
            'extent' => strlen($matches[0]),
            'element' => array(
                'name' => $emphasis,
                'handler' => array(
                    'function' => 'lineElements',
                    'argument' => $matches[1],
                    'destination' => 'elements',
                )
            ),
        );
    }

    protected function inlineEscapeSequence($Excerpt)
    {
        if (isset($Excerpt['text'][1]) and in_array($Excerpt['text'][1], $this->specialCharacters))
        {
            return array(
                'element' => array('rawHtml' => $Excerpt['text'][1]),
                'extent' => 2,
            );
        }
    }

    protected function inlineImage($Excerpt)
    {
        if ( ! isset($Excerpt['text'][1]) or $Excerpt['text'][1] !== '[')
        {
            return;
        }

        $Excerpt['text']= substr($Excerpt['text'], 1);

        $Link = $this->inlineLink($Excerpt);

        if ($Link === null)
        {
            return;
        }

        $inline = array(
            'extent' => $Link['extent'] + 1,
            'element' => array(
                'name' => 'img',
                'attributes' => array(
                    'src' => $Link['element']['attributes']['href'],
                    'alt' => $Link['element']['handler']['argument'],
                ),
                'autobreak' => true,
            ),
        );

        $inline['element']['attributes'] += $Link['element']['attributes'];

        unset($inline['element']['attributes']['href']);

        return $inline;
    }

    protected function inlineLink($Excerpt)
    {
        $element = array(
            'name' => 'a',
            'handler' => array(
                'function' => 'lineElements',
                'argument' => null,
                'destination' => 'elements',
            ),
            'nonNestables' => array('Url', 'Link'),
            'attributes' => array(
                'href' => null,
                'title' => null,
            ),
        );

        $extent = 0;

        $remainder = $Excerpt['text'];

        if (preg_match('/\[((?:[^][]++|(?R))*+)\]/', $remainder, $matches))
        {
            $element['handler']['argument'] = $matches[1];

            $extent += strlen($matches[0]);

            $remainder = substr($remainder, $extent);
        }
        else
        {
            return;
        }

        if (preg_match('/^[(]\s*+((?:[^ ()]++|[(][^ )]+[)])++)(?:[ ]+("[^"]*+"|\'[^\']*+\'))?\s*+[)]/', $remainder, $matches))
        {
            $element['attributes']['href'] = $matches[1];

            if (isset($matches[2]))
            {
                $element['attributes']['title'] = substr($matches[2], 1, - 1);
            }

            $extent += strlen($matches[0]);
        }
        else
        {
            if (preg_match('/^\s*\[(.*?)\]/', $remainder, $matches))
            {
                $definition = strlen($matches[1]) ? $matches[1] : $element['handler']['argument'];
                $definition = strtolower($definition);

                $extent += strlen($matches[0]);
            }
            else
            {
                $definition = strtolower($element['handler']['argument']);
            }

            if ( ! isset($this->definitionData['Reference'][$definition]))
            {
                return;
            }

            $definition = $this->definitionData['Reference'][$definition];

            $element['attributes']['href'] = $definition['url'];
            $element['attributes']['title'] = $definition['title'];
        }

        return array(
            'extent' => $extent,
            'element' => $element,
        );
    }

    protected function inlineMarkup($Excerpt)
    {
        if ($this->markupEscaped or $this->safeMode or strpos($Excerpt['text'], '>') === false)
        {
            return;
        }

        if ($Excerpt['text'][1] === '/' and preg_match('/^<\/\w[\w-]*+[ ]*+>/s', $Excerpt['text'], $matches))
        {
            return array(
                'element' => array('rawHtml' => $matches[0]),
                'extent' => strlen($matches[0]),
            );
        }

        if ($Excerpt['text'][1] === '!' and preg_match('/^<!---?[^>-](?:-?+[^-])*-->/s', $Excerpt['text'], $matches))
        {
            return array(
                'element' => array('rawHtml' => $matches[0]),
                'extent' => strlen($matches[0]),
            );
        }

        if ($Excerpt['text'][1] !== ' ' and preg_match('/^<\w[\w-]*+(?:[ ]*+'.$this->regexHtmlAttribute.')*+[ ]*+\/?>/s', $Excerpt['text'], $matches))
        {
            return array(
                'element' => array('rawHtml' => $matches[0]),
                'extent' => strlen($matches[0]),
            );
        }
    }

    protected function inlineSpecialCharacter($Excerpt)
    {
        if (substr($Excerpt['text'], 1, 1) !== ' ' and strpos($Excerpt['text'], ';') !== false
            and preg_match('/^&(#?+[0-9a-zA-Z]++);/', $Excerpt['text'], $matches)
        ) {
            return array(
                'element' => array('rawHtml' => '&' . $matches[1] . ';'),
                'extent' => strlen($matches[0]),
            );
        }

        return;
    }

    protected function inlineStrikethrough($Excerpt)
    {
        if ( ! isset($Excerpt['text'][1]))
        {
            return;
        }

        if ($Excerpt['text'][1] === '~' and preg_match('/^~~(?=\S)(.+?)(?<=\S)~~/', $Excerpt['text'], $matches))
        {
            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'del',
                    'handler' => array(
                        'function' => 'lineElements',
                        'argument' => $matches[1],
                        'destination' => 'elements',
                    )
                ),
            );
        }
    }

    protected function inlineUrl($Excerpt)
    {
        if ($this->urlsLinked !== true or ! isset($Excerpt['text'][2]) or $Excerpt['text'][2] !== '/')
        {
            return;
        }

        if (strpos($Excerpt['context'], 'http') !== false
            and preg_match('/\bhttps?+:[\/]{2}[^\s<]+\b\/*+/ui', $Excerpt['context'], $matches, PREG_OFFSET_CAPTURE)
        ) {
            $url = $matches[0][0];

            $inline = array(
                'extent' => strlen($matches[0][0]),
                'position' => $matches[0][1],
                'element' => array(
                    'name' => 'a',
                    'text' => $url,
                    'attributes' => array(
                        'href' => $url,
                    ),
                ),
            );

            return $inline;
        }
    }

    protected function inlineUrlTag($Excerpt)
    {
        if (strpos($Excerpt['text'], '>') !== false and preg_match('/^<(\w++:\/{2}[^ >]++)>/i', $Excerpt['text'], $matches))
        {
            $url = $matches[1];

            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'a',
                    'text' => $url,
                    'attributes' => array(
                        'href' => $url,
                    ),
                ),
            );
        }
    }

    protected function unmarkedText($text)
    {
        $inline = $this->inlineText($text);
        return $this->element($inline['element']);
    }

    protected function handle(array $element)
    {
        if (isset($element['handler']))
        {
            if (!isset($element['nonNestables']))
            {
                $element['nonNestables'] = array();
            }

            if (is_string($element['handler']))
            {
                $function = $element['handler'];
                $argument = $element['text'];
                unset($element['text']);
                $destination = 'rawHtml';
            }
            else
            {
                $function = $element['handler']['function'];
                $argument = $element['handler']['argument'];
                $destination = $element['handler']['destination'];
            }

            $element[$destination] = $this->{$function}($argument, $element['nonNestables']);

            if ($destination === 'handler')
            {
                $element = $this->handle($element);
            }

            unset($element['handler']);
        }

        return $element;
    }

    protected function handleElementRecursive(array $element)
    {
        return $this->elementApplyRecursive(array($this, 'handle'), $element);
    }

    protected function handleElementsRecursive(array $elements)
    {
        return $this->elementsApplyRecursive(array($this, 'handle'), $elements);
    }

    protected function elementApplyRecursive($closure, array $element)
    {
        $element = call_user_func($closure, $element);

        if (isset($element['elements']))
        {
            $element['elements'] = $this->elementsApplyRecursive($closure, $element['elements']);
        }
        elseif (isset($element['element']))
        {
            $element['element'] = $this->elementApplyRecursive($closure, $element['element']);
        }

        return $element;
    }

    protected function elementApplyRecursiveDepthFirst($closure, array $element)
    {
        if (isset($element['elements']))
        {
            $element['elements'] = $this->elementsApplyRecursiveDepthFirst($closure, $element['elements']);
        }
        elseif (isset($element['element']))
        {
            $element['element'] = $this->elementsApplyRecursiveDepthFirst($closure, $element['element']);
        }

        $element = call_user_func($closure, $element);

        return $element;
    }

    protected function elementsApplyRecursive($closure, array $elements)
    {
        foreach ($elements as &$element)
        {
            $element = $this->elementApplyRecursive($closure, $element);
        }

        return $elements;
    }

    protected function elementsApplyRecursiveDepthFirst($closure, array $elements)
    {
        foreach ($elements as &$element)
        {
            $element = $this->elementApplyRecursiveDepthFirst($closure, $element);
        }

        return $elements;
    }

    protected function element(array $element)
    {
        if ($this->safeMode)
        {
            $element = $this->sanitiseElement($element);
        }

        # identity map if element has no handler
        $element = $this->handle($element);

        $hasName = isset($element['name']);

        $markup = '';

        if ($hasName)
        {
            $markup .= '<' . $element['name'];

            if (isset($element['attributes']))
            {
                foreach ($element['attributes'] as $name => $value)
                {
                    if ($value === null)
                    {
                        continue;
                    }

                    $markup .= " $name=\"".self::escape($value).'"';
                }
            }
        }

        $permitRawHtml = false;

        if (isset($element['text']))
        {
            $text = $element['text'];
        }
        // very strongly consider an alternative if you're writing an
        // extension
        elseif (isset($element['rawHtml']))
        {
            $text = $element['rawHtml'];

            $allowRawHtmlInSafeMode = isset($element['allowRawHtmlInSafeMode']) && $element['allowRawHtmlInSafeMode'];
            $permitRawHtml = !$this->safeMode || $allowRawHtmlInSafeMode;
        }

        $hasContent = isset($text) || isset($element['element']) || isset($element['elements']);

        if ($hasContent)
        {
            $markup .= $hasName ? '>' : '';

            if (isset($element['elements']))
            {
                $markup .= $this->elements($element['elements']);
            }
            elseif (isset($element['element']))
            {
                $markup .= $this->element($element['element']);
            }
            else
            {
                if (!$permitRawHtml)
                {
                    $markup .= self::escape($text, true);
                }
                else
                {
                    $markup .= $text;
                }
            }

            $markup .= $hasName ? '</' . $element['name'] . '>' : '';
        }
        elseif ($hasName)
        {
            $markup .= ' />';
        }

        return $markup;
    }

    protected function elements(array $elements)
    {
        $markup = '';

        $autoBreak = true;

        foreach ($elements as $element)
        {
            if (empty($element))
            {
                continue;
            }

            $autoBreakNext = (isset($element['autobreak'])
                ? $element['autobreak'] : isset($element['name'])
            );
            // (autobreak === false) covers both sides of an element
            $autoBreak = !$autoBreak ? $autoBreak : $autoBreakNext;

            $markup .= ($autoBreak ? "\n" : '') . $this->element($element);
            $autoBreak = $autoBreakNext;
        }

        $markup .= $autoBreak ? "\n" : '';

        return $markup;
    }

    protected function li($lines)
    {
        $elements = $this->linesElements($lines);

        if ( ! in_array('', $lines)
            and isset($elements[0]) and isset($elements[0]['name'])
            and $elements[0]['name'] === 'p'
        ) {
            unset($elements[0]['name']);
        }

        return $elements;
    }

    /**
     * Replace occurrences $regexp with $elements in $text. Return an array of
     * elements representing the replacement.
     */
    protected static function pregReplaceElements($regexp, $elements, $text)
    {
        $newElements = array();

        while (preg_match($regexp, $text, $matches, PREG_OFFSET_CAPTURE))
        {
            $offset = $matches[0][1];
            $before = substr($text, 0, $offset);
            $after = substr($text, $offset + strlen($matches[0][0]));

            $newElements[] = array('text' => $before);

            foreach ($elements as $element)
            {
                $newElements[] = $element;
            }

            $text = $after;
        }

        $newElements[] = array('text' => $text);

        return $newElements;
    }

    #
    # Deprecated Methods
    #

    public function parse($text)
    {
        $markup = $this->text($text);

        return $markup;
    }

    protected function sanitiseElement(array $element)
    {
        static $goodAttribute = '/^[a-zA-Z0-9][a-zA-Z0-9-_]*+$/';
        static $safeUrlNameToAtt  = array(
            'a'   => 'href',
            'img' => 'src',
        );

        if ( ! isset($element['name']))
        {
            unset($element['attributes']);
            return $element;
        }

        if (isset($safeUrlNameToAtt[$element['name']]))
        {
            $element = $this->filterUnsafeUrlInAttribute($element, $safeUrlNameToAtt[$element['name']]);
        }

        if ( ! empty($element['attributes']))
        {
            foreach ($element['attributes'] as $att => $val)
            {
                # filter out badly parsed attribute
                if ( ! preg_match($goodAttribute, $att))
                {
                    unset($element['attributes'][$att]);
                }
                # dump onevent attribute
                elseif (self::striAtStart($att, 'on'))
                {
                    unset($element['attributes'][$att]);
                }
            }
        }

        return $element;
    }

    protected function filterUnsafeUrlInAttribute(array $element, $attribute)
    {
        foreach ($this->safeLinksWhitelist as $scheme)
        {
            if (self::striAtStart($element['attributes'][$attribute], $scheme))
            {
                return $element;
            }
        }

        $element['attributes'][$attribute] = str_replace(':', '%3A', $element['attributes'][$attribute]);

        return $element;
    }

    #
    # Static Methods
    #

    protected static function escape($text, $allowQuotes = false)
    {
        return htmlspecialchars($text, $allowQuotes ? ENT_NOQUOTES : ENT_QUOTES, 'UTF-8');
    }

    protected static function striAtStart($string, $needle)
    {
        $len = strlen($needle);

        if ($len > strlen($string))
        {
            return false;
        }
        else
        {
            return strtolower(substr($string, 0, $len)) === strtolower($needle);
        }
    }

    static function instance($name = 'default')
    {
        if (isset(self::$instances[$name]))
        {
            return self::$instances[$name];
        }

        $instance = new static();

        self::$instances[$name] = $instance;

        return $instance;
    }
}
