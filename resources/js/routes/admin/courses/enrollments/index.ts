import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::index
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:19
* @route '/admin/courses/{course}/enrollments'
*/
export const index = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/courses/{course}/enrollments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::index
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:19
* @route '/admin/courses/{course}/enrollments'
*/
index.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::index
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:19
* @route '/admin/courses/{course}/enrollments'
*/
index.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::index
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:19
* @route '/admin/courses/{course}/enrollments'
*/
index.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::index
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:19
* @route '/admin/courses/{course}/enrollments'
*/
const indexForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::index
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:19
* @route '/admin/courses/{course}/enrollments'
*/
indexForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::index
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:19
* @route '/admin/courses/{course}/enrollments'
*/
indexForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::store
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:50
* @route '/admin/courses/{course}/enrollments'
*/
export const store = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/courses/{course}/enrollments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::store
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:50
* @route '/admin/courses/{course}/enrollments'
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
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::store
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:50
* @route '/admin/courses/{course}/enrollments'
*/
store.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::store
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:50
* @route '/admin/courses/{course}/enrollments'
*/
const storeForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::store
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:50
* @route '/admin/courses/{course}/enrollments'
*/
storeForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::destroy
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:73
* @route '/admin/courses/{course}/enrollments/{enrollment}'
*/
export const destroy = (args: { course: number | { id: number }, enrollment: number | { id: number } } | [course: number | { id: number }, enrollment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/courses/{course}/enrollments/{enrollment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::destroy
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:73
* @route '/admin/courses/{course}/enrollments/{enrollment}'
*/
destroy.url = (args: { course: number | { id: number }, enrollment: number | { id: number } } | [course: number | { id: number }, enrollment: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            enrollment: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.id
        : args.course,
        enrollment: typeof args.enrollment === 'object'
        ? args.enrollment.id
        : args.enrollment,
    }

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{enrollment}', parsedArgs.enrollment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::destroy
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:73
* @route '/admin/courses/{course}/enrollments/{enrollment}'
*/
destroy.delete = (args: { course: number | { id: number }, enrollment: number | { id: number } } | [course: number | { id: number }, enrollment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::destroy
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:73
* @route '/admin/courses/{course}/enrollments/{enrollment}'
*/
const destroyForm = (args: { course: number | { id: number }, enrollment: number | { id: number } } | [course: number | { id: number }, enrollment: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CourseEnrollmentController::destroy
* @see app/Http/Controllers/Admin/CourseEnrollmentController.php:73
* @route '/admin/courses/{course}/enrollments/{enrollment}'
*/
destroyForm.delete = (args: { course: number | { id: number }, enrollment: number | { id: number } } | [course: number | { id: number }, enrollment: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const enrollments = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    destroy: Object.assign(destroy, destroy),
}

export default enrollments