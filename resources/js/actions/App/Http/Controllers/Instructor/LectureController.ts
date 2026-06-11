import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\LectureController::store
* @see app/Http/Controllers/Instructor/LectureController.php:13
* @route '/instructor/courses/{course}/sections/{section}/lectures'
*/
export const store = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/instructor/courses/{course}/sections/{section}/lectures',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\LectureController::store
* @see app/Http/Controllers/Instructor/LectureController.php:13
* @route '/instructor/courses/{course}/sections/{section}/lectures'
*/
store.url = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\LectureController::store
* @see app/Http/Controllers/Instructor/LectureController.php:13
* @route '/instructor/courses/{course}/sections/{section}/lectures'
*/
store.post = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::store
* @see app/Http/Controllers/Instructor/LectureController.php:13
* @route '/instructor/courses/{course}/sections/{section}/lectures'
*/
const storeForm = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::store
* @see app/Http/Controllers/Instructor/LectureController.php:13
* @route '/instructor/courses/{course}/sections/{section}/lectures'
*/
storeForm.post = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Instructor\LectureController::update
* @see app/Http/Controllers/Instructor/LectureController.php:33
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
export const update = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/instructor/courses/{course}/sections/{section}/lectures/{lecture}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Instructor\LectureController::update
* @see app/Http/Controllers/Instructor/LectureController.php:33
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
update.url = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            lecture: args[2],
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
        lecture: typeof args.lecture === 'object'
        ? args.lecture.id
        : args.lecture,
    }

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{lecture}', parsedArgs.lecture.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\LectureController::update
* @see app/Http/Controllers/Instructor/LectureController.php:33
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
update.put = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::update
* @see app/Http/Controllers/Instructor/LectureController.php:33
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
update.patch = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::update
* @see app/Http/Controllers/Instructor/LectureController.php:33
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
const updateForm = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::update
* @see app/Http/Controllers/Instructor/LectureController.php:33
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
updateForm.put = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::update
* @see app/Http/Controllers/Instructor/LectureController.php:33
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
updateForm.patch = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Instructor\LectureController::destroy
* @see app/Http/Controllers/Instructor/LectureController.php:52
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
export const destroy = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/instructor/courses/{course}/sections/{section}/lectures/{lecture}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Instructor\LectureController::destroy
* @see app/Http/Controllers/Instructor/LectureController.php:52
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
destroy.url = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            lecture: args[2],
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
        lecture: typeof args.lecture === 'object'
        ? args.lecture.id
        : args.lecture,
    }

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{lecture}', parsedArgs.lecture.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\LectureController::destroy
* @see app/Http/Controllers/Instructor/LectureController.php:52
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
destroy.delete = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::destroy
* @see app/Http/Controllers/Instructor/LectureController.php:52
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
const destroyForm = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\LectureController::destroy
* @see app/Http/Controllers/Instructor/LectureController.php:52
* @route '/instructor/courses/{course}/sections/{section}/lectures/{lecture}'
*/
destroyForm.delete = (args: { course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, lecture: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const LectureController = { store, update, destroy }

export default LectureController