<?php

function checkNumber($num) {
    if ($num > 0) {
        return "Positive";
    }
    if ($num < 0) {
        return "Negative";
    }
    return "Zero"; // Agar upar wali conditions false hain
}

echo checkNumber(5) . "<br>";  // Output: Positive (ek line mein)
echo checkNumber(-3) . "<br>"; // Output: Negative (next line mein)
echo checkNumber(0) . "<br>";  // Output: Zero (next line mein)
?>