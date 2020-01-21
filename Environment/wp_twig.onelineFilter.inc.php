<?php

/**
 * TWIG-filter |oneline
 * Fjerner linjeskift
 *
 * @param String $multiline
 * @return String $singelline
 */
function TWIGoneline(String $multiline)
{
    return str_replace(["\r", "\n"], '', $multiline);
}
