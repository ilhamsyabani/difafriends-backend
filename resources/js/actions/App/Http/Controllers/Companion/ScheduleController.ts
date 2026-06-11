import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Companion\ScheduleController::index
* @see app/Http/Controllers/Companion/ScheduleController.php:15
* @route '/companion/schedules'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/companion/schedules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::index
* @see app/Http/Controllers/Companion/ScheduleController.php:15
* @route '/companion/schedules'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::index
* @see app/Http/Controllers/Companion/ScheduleController.php:15
* @route '/companion/schedules'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::index
* @see app/Http/Controllers/Companion/ScheduleController.php:15
* @route '/companion/schedules'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::index
* @see app/Http/Controllers/Companion/ScheduleController.php:15
* @route '/companion/schedules'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::index
* @see app/Http/Controllers/Companion/ScheduleController.php:15
* @route '/companion/schedules'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::index
* @see app/Http/Controllers/Companion/ScheduleController.php:15
* @route '/companion/schedules'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Companion\ScheduleController::create
* @see app/Http/Controllers/Companion/ScheduleController.php:28
* @route '/companion/schedules/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/companion/schedules/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::create
* @see app/Http/Controllers/Companion/ScheduleController.php:28
* @route '/companion/schedules/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::create
* @see app/Http/Controllers/Companion/ScheduleController.php:28
* @route '/companion/schedules/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::create
* @see app/Http/Controllers/Companion/ScheduleController.php:28
* @route '/companion/schedules/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::create
* @see app/Http/Controllers/Companion/ScheduleController.php:28
* @route '/companion/schedules/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::create
* @see app/Http/Controllers/Companion/ScheduleController.php:28
* @route '/companion/schedules/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::create
* @see app/Http/Controllers/Companion/ScheduleController.php:28
* @route '/companion/schedules/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Companion\ScheduleController::store
* @see app/Http/Controllers/Companion/ScheduleController.php:35
* @route '/companion/schedules'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/companion/schedules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::store
* @see app/Http/Controllers/Companion/ScheduleController.php:35
* @route '/companion/schedules'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::store
* @see app/Http/Controllers/Companion/ScheduleController.php:35
* @route '/companion/schedules'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::store
* @see app/Http/Controllers/Companion/ScheduleController.php:35
* @route '/companion/schedules'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::store
* @see app/Http/Controllers/Companion/ScheduleController.php:35
* @route '/companion/schedules'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Companion\ScheduleController::show
* @see app/Http/Controllers/Companion/ScheduleController.php:0
* @route '/companion/schedules/{schedule}'
*/
export const show = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/companion/schedules/{schedule}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::show
* @see app/Http/Controllers/Companion/ScheduleController.php:0
* @route '/companion/schedules/{schedule}'
*/
show.url = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    if (Array.isArray(args)) {
        args = {
            schedule: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schedule: args.schedule,
    }

    return show.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::show
* @see app/Http/Controllers/Companion/ScheduleController.php:0
* @route '/companion/schedules/{schedule}'
*/
show.get = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::show
* @see app/Http/Controllers/Companion/ScheduleController.php:0
* @route '/companion/schedules/{schedule}'
*/
show.head = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::show
* @see app/Http/Controllers/Companion/ScheduleController.php:0
* @route '/companion/schedules/{schedule}'
*/
const showForm = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::show
* @see app/Http/Controllers/Companion/ScheduleController.php:0
* @route '/companion/schedules/{schedule}'
*/
showForm.get = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::show
* @see app/Http/Controllers/Companion/ScheduleController.php:0
* @route '/companion/schedules/{schedule}'
*/
showForm.head = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Companion\ScheduleController::edit
* @see app/Http/Controllers/Companion/ScheduleController.php:55
* @route '/companion/schedules/{schedule}/edit'
*/
export const edit = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/companion/schedules/{schedule}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::edit
* @see app/Http/Controllers/Companion/ScheduleController.php:55
* @route '/companion/schedules/{schedule}/edit'
*/
edit.url = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { schedule: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            schedule: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schedule: typeof args.schedule === 'object'
        ? args.schedule.id
        : args.schedule,
    }

    return edit.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::edit
* @see app/Http/Controllers/Companion/ScheduleController.php:55
* @route '/companion/schedules/{schedule}/edit'
*/
edit.get = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::edit
* @see app/Http/Controllers/Companion/ScheduleController.php:55
* @route '/companion/schedules/{schedule}/edit'
*/
edit.head = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::edit
* @see app/Http/Controllers/Companion/ScheduleController.php:55
* @route '/companion/schedules/{schedule}/edit'
*/
const editForm = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::edit
* @see app/Http/Controllers/Companion/ScheduleController.php:55
* @route '/companion/schedules/{schedule}/edit'
*/
editForm.get = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::edit
* @see app/Http/Controllers/Companion/ScheduleController.php:55
* @route '/companion/schedules/{schedule}/edit'
*/
editForm.head = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Companion\ScheduleController::update
* @see app/Http/Controllers/Companion/ScheduleController.php:64
* @route '/companion/schedules/{schedule}'
*/
export const update = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/companion/schedules/{schedule}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::update
* @see app/Http/Controllers/Companion/ScheduleController.php:64
* @route '/companion/schedules/{schedule}'
*/
update.url = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { schedule: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            schedule: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schedule: typeof args.schedule === 'object'
        ? args.schedule.id
        : args.schedule,
    }

    return update.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::update
* @see app/Http/Controllers/Companion/ScheduleController.php:64
* @route '/companion/schedules/{schedule}'
*/
update.put = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::update
* @see app/Http/Controllers/Companion/ScheduleController.php:64
* @route '/companion/schedules/{schedule}'
*/
update.patch = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::update
* @see app/Http/Controllers/Companion/ScheduleController.php:64
* @route '/companion/schedules/{schedule}'
*/
const updateForm = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::update
* @see app/Http/Controllers/Companion/ScheduleController.php:64
* @route '/companion/schedules/{schedule}'
*/
updateForm.put = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::update
* @see app/Http/Controllers/Companion/ScheduleController.php:64
* @route '/companion/schedules/{schedule}'
*/
updateForm.patch = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Companion\ScheduleController::destroy
* @see app/Http/Controllers/Companion/ScheduleController.php:85
* @route '/companion/schedules/{schedule}'
*/
export const destroy = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/companion/schedules/{schedule}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::destroy
* @see app/Http/Controllers/Companion/ScheduleController.php:85
* @route '/companion/schedules/{schedule}'
*/
destroy.url = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { schedule: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            schedule: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schedule: typeof args.schedule === 'object'
        ? args.schedule.id
        : args.schedule,
    }

    return destroy.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::destroy
* @see app/Http/Controllers/Companion/ScheduleController.php:85
* @route '/companion/schedules/{schedule}'
*/
destroy.delete = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::destroy
* @see app/Http/Controllers/Companion/ScheduleController.php:85
* @route '/companion/schedules/{schedule}'
*/
const destroyForm = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::destroy
* @see app/Http/Controllers/Companion/ScheduleController.php:85
* @route '/companion/schedules/{schedule}'
*/
destroyForm.delete = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\Companion\ScheduleController::toggleStatus
* @see app/Http/Controllers/Companion/ScheduleController.php:95
* @route '/companion/schedules/{schedule}/toggle-status'
*/
export const toggleStatus = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: toggleStatus.url(args, options),
    method: 'put',
})

toggleStatus.definition = {
    methods: ["put"],
    url: '/companion/schedules/{schedule}/toggle-status',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Companion\ScheduleController::toggleStatus
* @see app/Http/Controllers/Companion/ScheduleController.php:95
* @route '/companion/schedules/{schedule}/toggle-status'
*/
toggleStatus.url = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { schedule: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            schedule: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schedule: typeof args.schedule === 'object'
        ? args.schedule.id
        : args.schedule,
    }

    return toggleStatus.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Companion\ScheduleController::toggleStatus
* @see app/Http/Controllers/Companion/ScheduleController.php:95
* @route '/companion/schedules/{schedule}/toggle-status'
*/
toggleStatus.put = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: toggleStatus.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::toggleStatus
* @see app/Http/Controllers/Companion/ScheduleController.php:95
* @route '/companion/schedules/{schedule}/toggle-status'
*/
const toggleStatusForm = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Companion\ScheduleController::toggleStatus
* @see app/Http/Controllers/Companion/ScheduleController.php:95
* @route '/companion/schedules/{schedule}/toggle-status'
*/
toggleStatusForm.put = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggleStatus.form = toggleStatusForm

const ScheduleController = { index, create, store, show, edit, update, destroy, toggleStatus }

export default ScheduleController