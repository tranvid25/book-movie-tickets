<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CommentMovieController;
use App\Http\Controllers\Api\CommentNewsController;
use App\Http\Controllers\Api\FeedBackController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\newsController;
use App\Http\Controllers\Api\OrderDetailController;
use App\Http\Controllers\Api\PromotionNotificationController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\RapChieuController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\EmployeeController;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

        //user
        Route::get('laydanhsachuser', [UserController::class, 'index']);
        Route::get('laydanhsachuser/{id}', [UserController::class, 'show']);
        Route::post('laydanhsachuser', [UserController::class, 'store']);
        Route::post('laydanhsachuser/{id}/update', [UserController::class, 'update']);
        //Banner
        Route::get('laydanhsachbanner', [BannerController::class, 'index']);
        Route::post('laydanhsachbanner', [BannerController::class, 'store']);
        Route::get('laydanhsachbanner/{id}', [BannerController::class, 'show']);
        Route::post('laydanhsachbanner/{id}/update', [BannerController::class, 'update']);
        Route::delete('laydanhsachbanner/{id}/delete', [BannerController::class, 'destroy']);
        //Review New
        Route::get('laydanhsachbinhluan', [CommentNewsController::class, 'index']);
        Route::post('laydanhsachbinhluan', [CommentNewsController::class, 'store']);
        Route::get('laydanhsachbinhluan/{id}', [CommentNewsController::class, 'show']);
        Route::get('laydanhsachbinhluan/{id}/edit', [CommentNewsController::class, 'edit']);
        Route::post('laydanhsachbinhluan/{id}/update', [CommentNewsController::class, 'update']);
        Route::delete('laydanhsachbinhluan/{id}/delete', [CommentNewsController::class, 'destroy']);
        //Review movie;
        //FeedBack
        Route::get('laydanhsachfeedback', [FeedBackController::class, 'index']);
        Route::post('laydanhsachfeedback', [FeedBackController::class, 'store']);
        Route::get('laydanhsachfeedback/{id}', [FeedBackController::class, 'show']);
        Route::put('laydanhsachfeedback/{id}/update', [FeedBackController::class, 'update']);
        Route::delete('laydanhsachfeedback/{id}/delete', [FeedBackController::class, 'destroy']);
        //TIN TỨC
        Route::get('laydanhsachtintuc', [newsController::class, 'index']);
        Route::post('laydanhsachtintuc', [newsController::class, 'store']);
        Route::get('laydanhsachtintuc/{id}', [newsController::class, 'show']);
        Route::post('laydanhsachtintuc/{id}/update', [newsController::class, 'update']);
        Route::delete('laydanhsachtintuc/{id}/delete', [newsController::class, 'destroy']);
        //Phim
        Route::get('LayDanhSachPhim', [MovieController::class, 'index']);
        Route::post('LayDanhSachPhim', [MovieController::class, 'store']);
        Route::get('LayDanhSachPhim/{id}', [MovieController::class, 'show']);
        Route::get('LayDanhSachPhim/{id}/City', [MovieController::class, 'showCity']);
        Route::get('LayDanhSachPhim/rap/{id}', [MovieController::class, 'showrap']);
        Route::post('LayDanhSachPhim/{id}/update', [MovieController::class, 'update']);
        Route::delete('LayDanhSachPhim/{id}/delete', [MovieController::class, 'destroy']);

        //Order
        Route::get('laydanhsachdonhang', [OrderDetailController::class, 'index']);
        Route::post('laydanhsachdonhang', [OrderDetailController::class, 'store']);
        Route::get('laychitietdonhang/{id}', [OrderDetailController::class, 'show']);

        Route::get('doanhthu/{year}', [OrderDetailController::class, 'doanhthu']);
        //SEAT
        Route::get('laydanhsachghe', [SeatController::class, 'index']);
        Route::post('laydanhsachghe', [SeatController::class, 'store']);
        Route::get('laydanhsachghe/{id}', [SeatController::class, 'show']);
        Route::delete('laydanhsachghe/{id}/delete', [SeatController::class, 'destroy']);
        //Lịch Chiếu
        Route::get('laydanhsachghe/{maLichChieu}', [ShowtimeController::class, 'getSeatsByShowtime']);
        Route::get('laydanhsachlichchieu', [ShowtimeController::class, 'index']);
        Route::post('laydanhsachlichchieu', [ShowtimeController::class, 'store']);
        Route::get('laydanhsachlichchieu/{id}', [ShowtimeController::class, 'show']);
        Route::get('laylichchieutheophim/{id}', [ShowtimeController::class, 'showbyMovie']);
        Route::post('laydanhsachlichchieu/{id}/update', [ShowtimeController::class, 'update']);
        Route::delete('laydanhsachlichchieu/{id}/delete', [ShowtimeController::class, 'destroy']);
        //rapchieu
        Route::get('laydanhsachrap', [RapChieuController::class, 'index']);
        Route::post('laydanhsachrap', [RapChieuController::class, 'store']);
        Route::get('laydanhsachrap/{id}', [RapChieuController::class, 'show']);
        Route::post('laydanhsachrap/{id}/update', [RapChieuController::class, 'update']);
        Route::delete('laydanhsachrap/{id}/delete', [RapChieuController::class, 'destroy']);
        //Tỉnh
        Route::get('laydanhsachtinh', [ProvinceController::class, 'index']);
        Route::post('laydanhsachtinh', [ProvinceController::class, 'store']);
        //Search
        Route::get('/movies/search', [MovieController::class, 'search']);

        Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('passwordRetrieval', [AuthController::class, 'passwordRetrieval']);

    Route::group([
        'middleware' => 'auth:api','check.revoked.token'
    ], function () {
        Route::delete('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'user']);
        Route::get('laydanhsachbinhluanphim', [CommentMovieController::class, 'index']);
        Route::post('laydanhsachbinhluanphim', [CommentMovieController::class, 'store']);
        Route::get('laydanhsachbinhluanphim/{id}', [CommentMovieController::class, 'show']);
        Route::get('laydanhsachbinhluanphim/{id}/edit', [CommentMovieController::class, 'edit']);
        Route::post('laydanhsachbinhluanphim/{id}/update', [CommentMovieController::class, 'update']);
        Route::delete('laydanhsachbinhluanphim/{id}/delete', [CommentMovieController::class, 'destroy']);
        Route::get('laydanhsachbinhluan', [CommentNewsController::class, 'index']);
        Route::post('laydanhsachbinhluan', [CommentNewsController::class, 'store']);
        Route::get('laydanhsachbinhluan/{id}', [CommentNewsController::class, 'show']);
        Route::get('laydanhsachbinhluan/{id}/edit', [CommentNewsController::class, 'edit']);
        Route::post('laydanhsachbinhluan/{id}/update', [CommentNewsController::class, 'update']);
        Route::delete('laydanhsachbinhluan/{id}/delete', [CommentNewsController::class, 'destroy']);
        Route::get('/messages', [ChatController::class, 'index']);
        Route::post('/messages', [ChatController::class, 'store']);
        //Notification
        Route::get('notification/unread',[PromotionNotificationController::class,'unread']);
        Route::post('notification/create',[PromotionNotificationController::class,'store']);// admin tạo
        Route::put('notification/{id}/read',[PromotionNotificationController::class,'markAsRead']);
        //Order
        Route::get('laydanhsachdonhang/{id}', [OrderDetailController::class, 'showByUser']);
    });
});
