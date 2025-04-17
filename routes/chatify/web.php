<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\vendor\Chatify\MessagesController;
use App\Http\Controllers\ChatGroupController;

/*
* Main Chatify Route
*/
Route::get('/', [MessagesController::class, 'index'])->name(config('chatify.routes.prefix'));

/**
 *  Fetch info for specific id [user/group]
 */
Route::post('/idInfo', [MessagesController::class, 'idFetchData']);

Route::post('/groupidInfo', [ChatGroupController::class, 'idFetchData']);

Route::post('/create-group', [ChatGroupController::class, 'createGroup']);
/**
 * Send message routes
 */
Route::post('/sendMessage', [MessagesController::class, 'send'])->name('send.message');
Route::post('/sendMessageGroup', [ChatGroupController::class, 'send'])->name('send.groupMessage');

/**
 * Fetch messages
 */
Route::post('/fetchMessages', [MessagesController::class, 'fetch'])->name('fetch.messages');
Route::post('/fetchMessagesGroup', [ChatGroupController::class, 'fetchMessagesByGroup'])->name('fetch.messages.group');

/**
 * Download attachments route
 */
Route::get('/download/{fileName}', [MessagesController::class, 'download'])->name(config('chatify.attachments.download_route_name'));

/**
 * Authentication for pusher private channels
 */
Route::post('/chat/auth', [MessagesController::class, 'pusherAuth'])->name('pusher.auth');

/**
 * Make messages as seen
 */
Route::post('/makeSeen', [MessagesController::class, 'seen'])->name('messages.seen');

/**
 * Get contacts
 */
Route::get('/getContacts', [MessagesController::class, 'getContacts'])->name('contacts.get');
Route::get('/getGroups', [MessagesController::class, 'getGroups'])->name('contacts.get');

/**
 * Update contact item data
 */
Route::post('/updateContacts', [MessagesController::class, 'updateContactItem'])->name('contacts.update');
Route::post('/updateGroups', [MessagesController::class, 'updateGroupItem'])->name('contacts.update');

/**
 * Star in favorite list
 */
Route::post('/star', [MessagesController::class, 'favorite'])->name('star');

/**
 * Get favorites list
 */
Route::post('/favorites', [MessagesController::class, 'getFavorites'])->name('favorites');

/**
 * Search in messenger
 */
Route::get('/search', [MessagesController::class, 'search'])->name('search');

/**
 * Get shared photos
 */
Route::post('/shared', [MessagesController::class, 'sharedPhotos'])->name('shared');

/**
 * Delete conversation
 */
Route::post('/deleteConversation', [MessagesController::class, 'deleteConversation'])->name('conversation.delete');

/**
 * Delete message
 */
Route::post('/deleteMessage', [MessagesController::class, 'deleteMessage'])->name('message.delete');

/**
 * Update settings
 */
Route::post('/updateSettings', [MessagesController::class, 'updateSettings'])->name('avatar.update');

/**
 * Set active status
 */
Route::post('/setActiveStatus', [MessagesController::class, 'setActiveStatus'])->name('activeStatus.set');

/**
 * Group view by id
 */
Route::get('/group/{id}', [MessagesController::class, 'groupIndex'])->name('group');

/**
 * User view by id.
 * Note: Dynamic routes must always be at the end.
 */
Route::get('/{id}', [MessagesController::class, 'index'])->name('user');


