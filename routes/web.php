<?php

use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FlagController;
use App\Http\Controllers\GlossaryAnalyticsController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\GlossarySubjectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpactController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceAnalyticsController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResourceFileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SitewideAnalyticsController;
use App\Http\Controllers\StoryWeaverController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\SurveyAnswerController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyQuestionController;
use App\Http\Controllers\SurveyQuestionOptionController;
use App\Http\Controllers\SurveySettingController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\UserAnalyticsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VocabularyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Honeypot\ProtectAgainstSpam;

if (env('APP_ENV') === 'production') {
    \URL::forceScheme('https');
}

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::prefix(LaravelLocalization::setLocale())->middleware('localeSessionRedirect', 'localizationRedirect', 'localeViewPath')->group(function () {

    Route::get('login/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);

    Route::get('login/facebook', [LoginController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('login/facebook/callback', [LoginController::class, 'handleFacebookCallback']);
    /** ADD ALL LOCALIZED ROUTES INSIDE THIS GROUP **/
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/admin', [DashboardController::class, 'index'])->middleware('admin');
    // Users
    Route::get('admin/users', [UserController::class, 'index'])->middleware('admin');
    Route::post('admin/users', [UserController::class, 'index'])->name('user')->middleware('admin');
    Route::get('admin/users/users-data', [UserController::class, 'usersData'])->middleware('admin');
    Route::get('user/profile', [UserController::class, 'viewUser'])->where('userId', '[0-9]+')->name('user-view');
    Route::get('user/favorites', [UserController::class, 'favorites'])->where('userId', '[0-9]+')->name('user-favorites');
    Route::get('user/uploaded-resources', [UserController::class, 'uploadedResources'])->where('userId', '[0-9]+')->name('user-uploaded-resources');
    Route::post('user/update_profile', [UserController::class, 'updateProfile'])->name('user-profile-update');
    Route::get('admin/user/edit/{userId}', [UserController::class, 'edit'])->name('edit_user')->middleware('admin');
    Route::post('admin/user/update/{userId}', [UserController::class, 'update'])->name('update_user')->middleware('admin');
    Route::get('admin/user/delete/{userId}', [UserController::class, 'deleteUser'])->middleware('admin');
    Route::get('admin/user/export', [UserController::class, 'exportUsers'])->middleware('admin');
    Route::post('update-gender', [UserController::class, 'updateGender'])->name('update.gender');

    // Resources
    Route::get('admin/resources', [ResourceController::class, 'index'])->middleware('auth');
    Route::post('admin/resources', [ResourceController::class, 'index'])->name('resources')->middleware('admin');
    Route::get('resources/list', [ResourceController::class, 'list'])->name('resourceList');
    Route::get('resources/filter', [ResourceController::class, 'resourceFilter'])->name('resourceFilter');
    Route::get('resources/filter/subject', [ResourceController::class, 'getSubjectChildren']);
    Route::post('resources/filter/update-options', [ResourceController::class, 'updateFilterOptions'])->name('filter.update-options');
    Route::get('resources/priorities', [ReportController::class, 'resourcePriorities']);
    Route::get('resources/priorities/exclusion', [ReportController::class, 'resourcePrioritiesExclusion'])->middleware('LibraryManager');
    Route::post('resources/priorities/exclusion/add/{id}', [ReportController::class, 'resourcePrioritiesExclusionModify'])->middleware('LibraryManager');
    Route::post('resources/priorities/exclusion/remove/{id}', [ReportController::class, 'resourcePrioritiesExclusionModify'])->middleware('LibraryManager');
    Route::get('resource/{resourceId}', [ResourceController::class, 'viewPublicResource']);
    Route::post('resource/download_counter', [ResourceController::class, 'resourceDownloadCounter'])->middleware('auth');
    Route::get('resources', [ResourceController::class, 'list']);
    Route::get('resources/add/step1', [ResourceController::class, 'createStepOne'])->name('step1')->middleware('auth')->middleware('verified');
    Route::post('resources/add/step1', [ResourceController::class, 'postStepOne']);
    Route::get('resources/add/step2', [ResourceController::class, 'createStepTwo'])->name('step2')->middleware('auth')->middleware('verified');
    Route::post('resources/add/step2', [ResourceController::class, 'postStepTwo']);
    Route::get('resources/add/step3', [ResourceController::class, 'createStepThree'])->name('step3')->middleware('auth')->middleware('verified');
    Route::post('resources/add/step3', [ResourceController::class, 'postStepThree'])->middleware(ProtectAgainstSpam::class);
    Route::get('resources/attributes/{entity}', [ResourceController::class, 'attributes']);
    Route::post('resources/flag', [ResourceController::class, 'flag'])->name('flag');
    Route::post('resources/comment', [ResourceController::class, 'comment'])->name('comment')->middleware('auth')->middleware('verified');
    Route::get('admin/resource/published/{resourceId}', [ResourceController::class, 'published']);
    Route::get('admin/resource/delete/{resourceId}', [ResourceController::class, 'deleteResource'])->middleware('admin');
    Route::get('resources/edit/step1/{resourceId}', [ResourceController::class, 'createStepOneEdit'])->name('edit1')->middleware('LibraryManager');
    Route::post('resources/edit/step1/{resourceId}', [ResourceController::class, 'postStepOneEdit'])->middleware('LibraryManager');
    Route::get('resources/edit/step2/{resourceId}', [ResourceController::class, 'createStepTwoEdit'])->name('edit2')->middleware('LibraryManager');
    Route::post('resources/edit/step2/{resourceId}', [ResourceController::class, 'postStepTwoEdit'])->middleware('LibraryManager');
    Route::get('resources/edit/step3/{resourceId}', [ResourceController::class, 'createStepThreeEdit'])->name('edit3')->middleware('LibraryManager');
    Route::post('resources/edit/step3/{resourceId}', [ResourceController::class, 'postStepThreeEdit'])->middleware('LibraryManager');
    Route::post('resource/{resourceId}', [ResourceController::class, 'updateTid'])->middleware('admin')->name('updatetid');
    // delete a file
    Route::get('delete/file/{resourceId}/{fileName}', [ResourceController::class, 'deleteFile'])->name('delete-file')->middleware('LibraryManager');;
    // Contact
    Route::get('contact-us', [ContactController::class, 'create']);
    Route::post('contact-us', [ContactController::class, 'store'])->name('contact')->middleware(ProtectAgainstSpam::class);
    Route::get('admin/contacts', [ContactController::class, 'index'])->middleware('admin');
    Route::get('admin/contacts/read/{id}', [ContactController::class, 'read'])->middleware('admin');
    Route::get('admin/contacts/delete/{id}', [ContactController::class, 'delete'])->middleware('admin');
    // Report
    Route::get('admin/reports/impact_report', [ReportController::class, 'impactReport'])->name('impact-report')->middleware('admin');
    Route::get('admin/reports/ga', [ReportController::class, 'gaReport'])->middleware('admin');
    Route::get('admin/reports/resources', [ReportController::class, 'resourceReport'])->middleware('admin');
    Route::get('admin/reports/resources/subjects', [ReportController::class, 'resourceSubjectReport'])->middleware('admin');
    Route::get('admin/reports/languages', [ReportController::class, 'resourceLanguageReport'])->middleware('admin');
    // Downloads
    Route::get('admin/reports/downloads', [DownloadController::class, 'index'])->middleware('admin');
    Route::post('admin/reports/downloads', [DownloadController::class, 'index'])->name('downloads')->middleware('admin');
    Route::get('/about-education-afghanistan', function () {
        return redirect('page/22');
    });

    // Pages
    Route::controller(PageController::class)->group(function () {
        Route::get('page/{pageId}', 'view')->where('pageId', '[0-9]+');
        Route::middleware('admin')->group(function () {
            Route::prefix('admin')->group(function () {
                Route::get('pages', 'index');
                Route::get('get-pages', 'getPages')->name('getpages');
                Route::get('pages/view/{pageId}', 'view');
            });

            Route::prefix('page')->group(function () {
                Route::get('edit/{pageId}', 'edit');
                Route::post('update/{pageId}', 'update')->name('update_page');
                Route::get('create', 'create');
                Route::post('store', 'store')->name('add_page');
                Route::get('translate/{pageId}/{pageTnid}', 'translate');
                Route::get('add/translate/{pageId}/{lang}', 'addTranslate');
                Route::post('add/translate/{pageId}/{lang}', 'addPostTranslate')->name('add_page_translate');
            });
        });
    });

    // News
    Route::controller(NewsController::class)->group(function () {
        Route::get('news/{newsId}', 'view')->where('newsId', '[0-9]+');
        Route::middleware('admin')->group(function () {

            Route::get('admin/news', 'index');
            Route::get('admin/get-news', 'getNews')->name('getnews');

            Route::prefix('news')->group(function () {
                Route::get('edit/{newsId}', 'edit');
                Route::post('update/{newsId}', 'update')->name('update_news');
                Route::get('create', 'create');
                Route::post('store', 'store')->name('add_news');
                Route::get('translate/{newsId}/{newsTnid}', 'translate');
                Route::get('add/translate/{newsId}/{lang}', 'addTranslate');
                Route::post('add/translate/{newsId}/{lang}', 'addPostTranslate')->name('add_news_translate');
            });
        });
    });
    // Menu
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::controller(MenuController::class)->group(function () {
            Route::get('menu', 'index');
            Route::post('menu', 'index')->name('menulist');
            Route::get('menu/add/{menuId}', 'create');
            Route::post('menu/store', 'store')->name('store_menu');
            Route::get('menu/edit/{menuId}', 'edit');
            Route::post('menu/update/{menuId}', 'update')->name('update_menu');
            Route::get('menu/translate/{menuId}', 'translate');
            Route::delete('menu/{menuId}', 'destroy')->name('delete_menu');
            Route::get('menu/sort', 'sort')->name('sort_menu');
            Route::get('menu/ajax_get_parents', 'ajaxGetParents')->name('ajax_get_parents');
        });

        // Settings
        Route::controller(SettingController::class)->group(function () {
            Route::get('settings', 'edit');
            Route::put('settings/{setting}', 'update');
        });

        Route::resource('subscribers', SubscriberController::class)->only('index', 'destroy');

        // Comments
        Route::prefix('comments')->controller(CommentController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('delete/{resourceComment}', 'delete');
            Route::get('published/{resourceComment}', 'published')->middleware('admin');
        });

        // Flags
        Route::get('flags', [FlagController::class, 'index']);
        // Taxonomy
        Route::prefix('taxonomy')->controller(TaxonomyController::class)->group(function () {
            Route::get('', 'index')->name('gettaxonomylist')->middleware('admin');
            Route::post('', 'index')->name('posttaxonomylist')->middleware('admin');
            Route::get('edit/{vid}/{tid}', 'edit')->name('taxonomyedit');
            Route::post('update/{vid}/{tid}', 'update')->name('update-taxonomy');
            Route::get('translate/{tid}', 'translate');
            Route::get('create', 'create')->name('taxonomycreate');
            Route::post('store', 'store')->name('taxonomystore');
            Route::get('create-translate/{tid}/{tnid}/{lang}', 'createTranslate')->name('taxonomytranslatecreate');
            Route::post('store-translate/{tnid}', 'storeTranslate')->name('taxonomytranslatestore');

            // Subject areas routes
            Route::get('subject-areas', 'subjectAreas')->name('subject_areas.index');
            Route::get('subject-area/edit-or-create/{tnid?}', 'editOrCreateSubjectArea')->where('tnid', '[1-9][0-9]*')->name('subject_area.edit_or_create');
            Route::post('subject-area', 'storeOrUpdateSubjectArea')->name('subject_area.store_or_update');
        });

        // Taxonomy Vocabulary
        Route::get('vocabulary', [VocabularyController::class, 'index'])->name('vocabularylist');
        Route::get('vocabularies', [VocabularyController::class, 'getVocabularies'])->name('getvocabularies');
        Route::get('vocabulary/create', [VocabularyController::class, 'create'])->name('vocabularycreate');
        Route::post('vocabulary/store', [VocabularyController::class, 'store'])->name('vocabularystore');
        Route::get('vocabulary/edit/{vid}', [VocabularyController::class, 'edit'])->name('vocabularyedit');
        Route::post('vocabulary/edit/{vid}', [VocabularyController::class, 'update'])->name('update-vocabulary');
        // Sync
        Route::get('/sync', [SyncController::class, 'index'])->middleware('admin');
        Route::get('/run_sync', [SyncController::class, 'SyncIt'])->middleware('admin');;
    });

    // Glossary
    Route::prefix('glossary')->controller(GlossaryController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'index')->name('glossary');
        Route::prefix('glossary')->middleware('LibraryManager')->group(function () {
            Route::get('create', 'create')->name('glossary_create');
            Route::post('store', 'store')->name('glossary_store');
            Route::post('update', 'update')->name('glossary_update');
            Route::post('delete/{id}', 'destroy')->name('glossary_delete');
            Route::post('approve/{id}', 'approve')->name('glossary_approve');
        });
    });

    // Impact Page
    Route::get('/impact/{update?}', [ImpactController::class, 'index']);

    // admin, survey
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::controller(SurveyController::class)->group(function () {
            Route::get('surveys', 'index');
            Route::get('survey/edit/{id}', 'edit');
            Route::get('survey/view/{id}/{tnid}', 'view');
            Route::get('survey/report/{id}', 'report');
            Route::get('survey/add/translate/{id}/{lang}', 'addTranslate');
            Route::get('survey/create', 'create');
            Route::get('survey/delete/{id}', 'delete');
            Route::post('update_survey/{id}', 'update')->name('update_survey');
            Route::post('survey/create', 'store')->name('create_survey');
        });

        // question
        Route::controller(SurveyQuestionController::class)->group(function () {
            Route::get('survey/questions/{id}', 'index');
            Route::get('survey/{surveyid}/question/view/{id}/{tnid}', 'view');
            Route::get('survey/question/add/translate/{id}/{lang}', 'addTranslate');
            Route::post('survey/question/add', 'store')->name('create_question');
            Route::get('survey/question/add/{id}', 'create');
            Route::get('survey/question/delete/{id}', 'delete');
        });

        // option
        Route::controller(SurveyQuestionOptionController::class)->group(function () {
            Route::get('survey/question/option/delete/{id}', 'delete');
            Route::get('survey/{survey_id}/question/{id}/view_options', 'index');
            Route::get('survey/question/{questionid}/option/{optionid}/view/{tnid}', 'view');
            Route::get('survey/question/option/add/translate/{id}/{lang}', 'addTranslate');
            Route::get('survey/{survey_id}/question/{id}/option/create', 'create');
            Route::post('survey/question/option/add', 'store')->name('create_option');
        });

        // result
        Route::controller(SurveyAnswerController::class)->group(function () {
            Route::get('survey_questions', 'allQuestions');
            Route::get('survey_question/answers/{id}', 'questionAnswers');
        });

        // setting
        Route::controller(SurveySettingController::class)->group(function () {
            Route::get('survey_time', 'getSurveyModalTime');
            Route::get('edit_survey_modal_time', 'editSurveyModalTime');
            Route::post('update_survey_modal_time/{id}', 'updateSurveyModalTime')->name('update_survey_modal_time');
            Route::get('create_survey_modal_time', 'createSurveyModalTime');
            Route::post('store_survey_modal_time', 'storeSurveyModalTime')->name('store_survey_modal_time');
        });
    });

    Route::post('/survey/store', [SurveyAnswerController::class, 'storeUserSurvey'])->name('survey')->middleware('admin');
    // Analytics
    Route::prefix('admin/analytics')->middleware('admin')->group(function () {
        Route::controller(AnalyticsController::class)->group(function () {
            Route::get('')->name('analytics-list');
            Route::post('')->name('analytics');
        });
    });
    // admin, glossary
    Route::prefix('admin/glossary_subjects')->middleware('admin')->group(function () {
        Route::controller(GlossarySubjectController::class)->group(function () {
            Route::get('', 'index')->name('glossary_subjects_list');
            Route::get('create', 'create')->name('glossary_subjects_create');
            Route::get('edit/{id}', 'edit')->name('glossary_subjects_edit');
            Route::post('update', 'update')->name('glossary_subjects_update');
        });
    });

    // StoryWeaver
    Route::get('/storyweaver/confirm/{landing_page}', [StoryWeaverController::class, 'storyWeaverConfirmation'])->name('storyweaver-confirm')->middleware('auth')->middleware('verified');
    Route::get('/storyweaver/auth', [StoryWeaverController::class, 'storyWeaverAuth'])->name('storyweaver-auth')->middleware('auth')->middleware('verified');
    // Adding old DDL routes
    Route::get('/user/register', [RegisterController::class, 'showRegistrationForm']);
    Route::get('/user', [LoginController::class, 'showLoginForm']);
    Route::get('/access-library', [ResourceController::class, 'createStepOne'])->middleware('auth')->middleware('verified');
    Route::get('/node/add', [ResourceController::class, 'createStepOne'])->middleware('auth')->middleware('verified');
    Route::get('/node/add/resourcefile', [ResourceController::class, 'createStepOne'])->middleware('auth')->middleware('verified');
    Route::get('/add/resourcefile', [ResourceController::class, 'createStepOne'])->middleware('auth')->middleware('verified');
    Route::get('/node/{resourceId}', [ResourceController::class, 'viewPublicResource']);
    Route::get('/user/logout', [LoginController::class, 'logout']);
    Route::get('/user/password', [ForgotPasswordController::class, 'showLinkRequestForm']);
    Route::get('/volunteer', function () {
        return redirect('page/1532');
    });
    Route::get('/support-library', function () {
        return redirect('page/21');
    });
    // Auth
    Route::middleware(ProtectAgainstSpam::class)->group(function () {
        Auth::routes(['verify' => true]);
    });
    Route::get('/logout', function () {
        Auth::logout();

        return redirect('/home');
    });
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::prefix('subscribe')->middleware(['auth', 'verified'])->controller(SubscribeController::class)->group(function () {
        Route::get('/', 'index')->name('subscribe.index');
        Route::post('', 'store')->name('subscribe.store');
    });

    // Analytics
    Route::prefix('admin/analytics')->middleware('admin')->group(function () {
        Route::get('user', [UserAnalyticsController::class, 'index']);
        Route::get('resource', [ResourceAnalyticsController::class, 'index']);
        Route::controller(SitewideAnalyticsController::class)->group(function () {
            Route::get('sitewide', 'index');
            Route::get('reports/sitewide', 'view');
        });

        Route::controller(GlossaryAnalyticsController::class)->group(function () {
            Route::get('glossary', 'index');
            Route::get('reports/glossary', 'view');
        });
    });

    Route::controller(PrivacyPolicyController::class)->group(function () {
        Route::get('privacy-policy', 'index')->name('privacy-policy');
        Route::get('mobile-privacy-policy', 'mobilePrivacyPolicy')->name('mobile-privacy-policy');
        Route::get('opt-out', 'optOut')->name('opt-out');
    });
});

Route::post('/upload-image', [ResourceFileController::class, 'uploadImage'])->name('upload.image');
Route::get('/search-images', [ResourceFileController::class, 'searchImages'])->name('search.images');
Route::post('upload-image-from-editor', [FileController::class, 'uploadtImageFromEditor'])->name('upload.image.from.editor')->middleware('auth');

/** OTHER PAGES THAT SHOULD NOT BE LOCALIZED **/
Route::post('resources/favorite', [ResourceController::class, 'resourceFavorite']);
Route::get('/storage/{resource_id}/{file_id}/{file_name}', FileController::class)->where(['file_name' => '.*']);
