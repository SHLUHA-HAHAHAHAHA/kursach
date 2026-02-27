<?php
function is_valid_message($text) {
    if (preg_match('/[0-9]{5,}/', $text) || preg_match('/[^aeiouyаеёиоуыэюя\s]{5,}/ui', $text)) {
        var_dump("Нет смысла");
    }

    $bad_words = ['сука', 'блять', 'пидор'];
    foreach ($bad_words as $word) {
        if (mb_stripos($text, $word) !== false) {
            return false;
        }
    }

    if (mb_strlen($text) < 5) {
        return false;
    }

    return true;
}
