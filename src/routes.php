<?php
Route::bind('galleries', function ($value) {
    return TypiCMS\Modules\Galleries\Models\Gallery::where('id', $value)
        ->with('translations')
        ->firstOrFail();
});

if (! App::runningInConsole()) {
    Route::group(
        array(
            'before'    => 'visitor.publicAccess',
            'namespace' => 'TypiCMS\Modules\Galleries\Http\Controllers',
        ),
        function () {
            $routes = app('TypiCMS.routes');
            foreach (Config::get('translatable.locales') as $lang) {
                if (isset($routes['galleries'][$lang])) {
                    $uri = $routes['galleries'][$lang];
                } else {
                    $uri = 'galleries';
                    if (Config::get('app.fallback_locale') != $lang || config('typicms.main_locale_in_url')) {
                        $uri = $lang . '/' . $uri;
                    }
                }
                Route::get($uri, array('as' => $lang.'.galleries', 'uses' => 'PublicController@index'));
                Route::get($uri.'/{slug}', array('as' => $lang.'.galleries.slug', 'uses' => 'PublicController@show'));
            }
        }
    );
}

Route::group(
    array(
        'namespace' => 'TypiCMS\Modules\Galleries\Http\Controllers',
        'prefix'    => 'admin',
    ),
    function () {
        Route::resource('galleries', 'AdminController');
    }
);

Route::group(['prefix'=>'api'], function() {
    Route::resource('galleries', 'ApiController');
});
