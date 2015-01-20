<?php
/*
 * Test Script
 */

// require autoloader
require_once('../private/system/loader.php');

// test insert
$adminInsert = \Model\User::createAdmin('test@gmail.com', 'testing123');

if($adminInsert == null)
{
    echo "Admin insert failed\n";
    die();
}

// test select
$adminSelect = \Model\User::getById($adminInsert->getId());

if($adminSelect == null)
{
    echo "Admin select failed\n";
    die();
}

// test authenticate
$adminAuth = \Model\User::authenticate('test@gmail.com', 'testing123');

if($adminAuth == null)
{
    echo "Admin auth failed\n";
    die();
}

// test update
$adminAuth->setEmail('test2@gmail.com')->update();
$adminSelect = \Model\User::getById($adminAuth->getId());

if($adminSelect->getEmail() != 'test2@gmail.com')
{
    echo "Admin update failed\n";
    die();
}

// test delete
$adminAuth->delete();
$adminSelect = \Model\User::getById($adminInsert->getId());

if($adminSelect != null)
{
    echo "Admin delete failed\n";
    die();
}

echo "Admin tests passed\n";