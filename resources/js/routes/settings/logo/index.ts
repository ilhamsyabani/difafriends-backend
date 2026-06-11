import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\LogoController::edit
* @see app/Http/Controllers/Settings/LogoController.php:15
* @route '/settings/logo'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/logo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\LogoController::edit
* @see app/Http/Controllers/Settings/LogoController.php:15
* @route '/settings/logo'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\LogoController::edit
* @see app/Http/Controllers/Settings/LogoController.php:15
* @route '/settings/logo'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::edit
* @see app/Http/Controllers/Settings/LogoController.php:15
* @route '/settings/logo'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::edit
* @see app/Http/Controllers/Settings/LogoController.php:15
* @route '/settings/logo'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::edit
* @see app/Http/Controllers/Settings/LogoController.php:15
* @route '/settings/logo'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::edit
* @see app/Http/Controllers/Settings/LogoController.php:15
* @route '/settings/logo'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Settings\LogoController::update
* @see app/Http/Controllers/Settings/LogoController.php:25
* @route '/settings/logo'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/settings/logo',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\LogoController::update
* @see app/Http/Controllers/Settings/LogoController.php:25
* @route '/settings/logo'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\LogoController::update
* @see app/Http/Controllers/Settings/LogoController.php:25
* @route '/settings/logo'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::update
* @see app/Http/Controllers/Settings/LogoController.php:25
* @route '/settings/logo'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::update
* @see app/Http/Controllers/Settings/LogoController.php:25
* @route '/settings/logo'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Settings\LogoController::destroy
* @see app/Http/Controllers/Settings/LogoController.php:48
* @route '/settings/logo'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/logo',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\LogoController::destroy
* @see app/Http/Controllers/Settings/LogoController.php:48
* @route '/settings/logo'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\LogoController::destroy
* @see app/Http/Controllers/Settings/LogoController.php:48
* @route '/settings/logo'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::destroy
* @see app/Http/Controllers/Settings/LogoController.php:48
* @route '/settings/logo'
*/
const destroyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\LogoController::destroy
* @see app/Http/Controllers/Settings/LogoController.php:48
* @route '/settings/logo'
*/
destroyForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const logo = {
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default logo