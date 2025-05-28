<?php
session_name('tourman');
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {session_regenerate_id(true);}
