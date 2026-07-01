<?php
// ১. টাইম আউট আনলিমিটেড করা (যাতে লাইভ স্ট্রিমটি দীর্ঘ সময় চললেও বন্ধ না হয়)
set_time_limit(0);

// ২. আউটপুট বাফারিং বন্ধ করা (যাতে ভিডিও ডেটা সার্ভারে জমা না হয়ে সাথে সাথে ব্রাউজারে চলে যায়)
if (ob_get_level()) {
    ob_end_clean();
}

// ৩. আপনার মূল অনিরাপদ (HTTP) লাইভ স্ট্রিম লিংক
$stream_url = "http://fastshare1.com:8080/live/25711345/late8airline/52500.ts";

// ৪. ব্রাউজার ও প্লেয়ারের জন্য প্রয়োজনীয় হেডার সেট করা
header("Content-Type: video/mp2t");               // .ts ফাইলের জন্য সঠিক মিডিয়া টাইপ
header("Access-Control-Allow-Origin: *");         // CORS পলিসি বাইপাস করার জন্য
header("Cache-Control: no-cache, must-revalidate"); // ভিডিও যেন ক্যাশ (Cache) না হয়
header("Connection: keep-alive");                 // কানেকশন সচল রাখার জন্য

// ৫. সার্ভার থেকে ভিডিও ডেটা রিড করে ব্রাউজারে পাঠানো
$handle = fopen($stream_url, "rb");

if ($handle) {
    // যতক্ষণ সোর্স সার্ভার থেকে ডেটা আসবে এবং ইউজার সাইটে যুক্ত থাকবে
    while (!feof($handle) && !connection_aborted()) {
        // প্রতিবারে ৮ কিলোবাইট (8192 bytes) করে ডেটা রিড করে ব্রাউজারে পাঠানো
        echo fread($handle, 8192);
        flush(); // ব্রাউজারে ডেটা সাথে সাথে পুশ করার জন্য
    }
    fclose($handle);
} else {
    // যদি কোনো কারণে সোর্স সার্ভার থেকে ভিডিও না পাওয়া যায়
    header("HTTP/1.0 500 Internal Server Error");
    echo "Error: লাইভ স্ট্রিম সোর্সের সাথে সংযোগ করা যাচ্ছে না।";
}
?>
