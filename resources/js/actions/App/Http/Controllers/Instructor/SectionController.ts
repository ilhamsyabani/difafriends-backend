import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\SectionController::store
* @see app/Http/Controllers/Instructor/SectionController.php:12
* @route '/instructor/courses/{course}/sections'
*/
export const store = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/instructor/courses/{course}/sections',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\SectionController::store
* @see app/Http/Controllers/Instructor/SectionController.php:12
* @route '/instructor/courses/{course}/sections'
*/
store.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { course: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.id
        : args.course,
    }

    return store.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\SectionController::store
* @see app/Http/Controllers/Instructor/SectionController.php:12
* @route '/instructor/courses/{course}/sections'
*/
store.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::store
* @see app/Http/Controllers/Instructor/SectionController.php:12
* @route '/instructor/courses/{course}/sections'
*/
const storeForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::store
* @see app/Http/Controllers/Instructor/SectionController.php:12
* @route '/instructor/courses/{course}/sections'
*/
storeForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Instructor\SectionController::update
* @see app/Http/Controllers/Instructor/SectionController.php:26
* @route '/instructor/courses/{course}/sections/{section}'
*/
export const update = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/instructor/courses/{course}/sections/{section}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Instructor\SectionController::update
* @see app/Http/Controllers/Instructor/SectionController.php:26
* @route '/instructor/courses/{course}/sections/{section}'
*/
update.url = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.id
        : args.course,
        section: typeof args.section === 'object'
        ? args.section.id
        : args.section,
    }

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\SectionController::update
* @see app/Http/Controllers/Instructor/SectionController.php:26
* @route '/instructor/courses/{course}/sections/{section}'
*/
update.put = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::update
* @see app/Http/Controllers/Instructor/SectionController.php:26
* @route '/instructor/courses/{course}/sections/{section}'
*/
update.patch = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::update
* @see app/Http/Controllers/Instructor/SectionController.php:26
* @route '/instructor/courses/{course}/sections/{section}'
*/
const updateForm = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::update
* @see app/Http/Controllers/Instructor/SectionController.php:26
* @route '/instructor/courses/{course}/sections/{section}'
*/
updateForm.put = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::update
* @see app/Http/Controllers/Instructor/SectionController.php:26
* @route '/instructor/courses/{course}/sections/{section}'
*/
updateForm.patch = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Instructor\SectionController::destroy
* @see app/Http/Controllers/Instructor/SectionController.php:40
* @route '/instructor/courses/{course}/sections/{section}'
*/
export const destroy = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/instructor/courses/{course}/sections/{section}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Instructor\SectionController::destroy
* @see app/Http/Controllers/Instructor/SectionController.php:40
* @route '/instructor/courses/{course}/sections/{section}'
*/
destroy.url = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.id
        : args.course,
        section: typeof args.section === 'object'
        ? args.section.id
        : args.section,
    }

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\SectionController::destroy
* @see app/Http/Controllers/Instructor/SectionController.php:40
* @route '/instructor/courses/{course}/sections/{section}'
*/
destroy.delete = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::destroy
* @see app/Http/Controllers/Instructor/SectionController.php:40
* @route '/instructor/courses/{course}/sections/{section}'
*/
const destroyForm = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\SectionController::destroy
* @see app/Http/Controllers/Instructor/SectionController.php:40
* @route '/instructor/courses/{course}/sections/{section}'
*/
destroyForm.delete = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const SectionController = { store, update, destroy }

export default SectionController