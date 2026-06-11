import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
import questions from './questions'
/**
* @see \App\Http\Controllers\Instructor\QuizController::create
* @see app/Http/Controllers/Instructor/QuizController.php:17
* @route '/instructor/courses/{course}/sections/{section}/quiz/create'
*/
export const create = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\QuizController::create
* @see app/Http/Controllers/Instructor/QuizController.php:17
* @route '/instructor/courses/{course}/sections/{section}/quiz/create'
*/
create.url = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return create.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizController::create
* @see app/Http/Controllers/Instructor/QuizController.php:17
* @route '/instructor/courses/{course}/sections/{section}/quiz/create'
*/
create.get = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::create
* @see app/Http/Controllers/Instructor/QuizController.php:17
* @route '/instructor/courses/{course}/sections/{section}/quiz/create'
*/
create.head = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::create
* @see app/Http/Controllers/Instructor/QuizController.php:17
* @route '/instructor/courses/{course}/sections/{section}/quiz/create'
*/
const createForm = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::create
* @see app/Http/Controllers/Instructor/QuizController.php:17
* @route '/instructor/courses/{course}/sections/{section}/quiz/create'
*/
createForm.get = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::create
* @see app/Http/Controllers/Instructor/QuizController.php:17
* @route '/instructor/courses/{course}/sections/{section}/quiz/create'
*/
createForm.head = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Instructor\QuizController::store
* @see app/Http/Controllers/Instructor/QuizController.php:35
* @route '/instructor/courses/{course}/sections/{section}/quiz'
*/
export const store = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/instructor/courses/{course}/sections/{section}/quiz',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\QuizController::store
* @see app/Http/Controllers/Instructor/QuizController.php:35
* @route '/instructor/courses/{course}/sections/{section}/quiz'
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
* @see \App\Http\Controllers\Instructor\QuizController::store
* @see app/Http/Controllers/Instructor/QuizController.php:35
* @route '/instructor/courses/{course}/sections/{section}/quiz'
*/
store.post = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::store
* @see app/Http/Controllers/Instructor/QuizController.php:35
* @route '/instructor/courses/{course}/sections/{section}/quiz'
*/
const storeForm = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::store
* @see app/Http/Controllers/Instructor/QuizController.php:35
* @route '/instructor/courses/{course}/sections/{section}/quiz'
*/
storeForm.post = (args: { course: number | { id: number }, section: number | { id: number } } | [course: number | { id: number }, section: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Instructor\QuizController::edit
* @see app/Http/Controllers/Instructor/QuizController.php:59
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit'
*/
export const edit = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\QuizController::edit
* @see app/Http/Controllers/Instructor/QuizController.php:59
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit'
*/
edit.url = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            quiz: args[2],
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
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return edit.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizController::edit
* @see app/Http/Controllers/Instructor/QuizController.php:59
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit'
*/
edit.get = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::edit
* @see app/Http/Controllers/Instructor/QuizController.php:59
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit'
*/
edit.head = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::edit
* @see app/Http/Controllers/Instructor/QuizController.php:59
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit'
*/
const editForm = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::edit
* @see app/Http/Controllers/Instructor/QuizController.php:59
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit'
*/
editForm.get = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::edit
* @see app/Http/Controllers/Instructor/QuizController.php:59
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/edit'
*/
editForm.head = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Instructor\QuizController::update
* @see app/Http/Controllers/Instructor/QuizController.php:72
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
export const update = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Instructor\QuizController::update
* @see app/Http/Controllers/Instructor/QuizController.php:72
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
update.url = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            quiz: args[2],
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
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizController::update
* @see app/Http/Controllers/Instructor/QuizController.php:72
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
update.put = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::update
* @see app/Http/Controllers/Instructor/QuizController.php:72
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
update.patch = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::update
* @see app/Http/Controllers/Instructor/QuizController.php:72
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
const updateForm = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::update
* @see app/Http/Controllers/Instructor/QuizController.php:72
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
updateForm.put = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::update
* @see app/Http/Controllers/Instructor/QuizController.php:72
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
updateForm.patch = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Instructor\QuizController::destroy
* @see app/Http/Controllers/Instructor/QuizController.php:89
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
export const destroy = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Instructor\QuizController::destroy
* @see app/Http/Controllers/Instructor/QuizController.php:89
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
destroy.url = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            quiz: args[2],
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
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizController::destroy
* @see app/Http/Controllers/Instructor/QuizController.php:89
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
destroy.delete = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::destroy
* @see app/Http/Controllers/Instructor/QuizController.php:89
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
const destroyForm = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizController::destroy
* @see app/Http/Controllers/Instructor/QuizController.php:89
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}'
*/
destroyForm.delete = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const quiz = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    questions: Object.assign(questions, questions),
}

export default quiz