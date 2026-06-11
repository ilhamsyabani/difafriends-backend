import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::create
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create'
*/
export const create = (args: { course: string | number, section: string | number, quiz: string | number } | [course: string | number, section: string | number, quiz: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::create
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create'
*/
create.url = (args: { course: string | number, section: string | number, quiz: string | number } | [course: string | number, section: string | number, quiz: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            quiz: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: args.course,
        section: args.section,
        quiz: args.quiz,
    }

    return create.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::create
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create'
*/
create.get = (args: { course: string | number, section: string | number, quiz: string | number } | [course: string | number, section: string | number, quiz: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::create
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create'
*/
create.head = (args: { course: string | number, section: string | number, quiz: string | number } | [course: string | number, section: string | number, quiz: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::create
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create'
*/
const createForm = (args: { course: string | number, section: string | number, quiz: string | number } | [course: string | number, section: string | number, quiz: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::create
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create'
*/
createForm.get = (args: { course: string | number, section: string | number, quiz: string | number } | [course: string | number, section: string | number, quiz: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::create
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/create'
*/
createForm.head = (args: { course: string | number, section: string | number, quiz: string | number } | [course: string | number, section: string | number, quiz: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Instructor\QuizQuestionController::store
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:14
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions'
*/
export const store = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::store
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:14
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions'
*/
store.url = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::store
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:14
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions'
*/
store.post = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::store
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:14
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions'
*/
const storeForm = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::store
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:14
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions'
*/
storeForm.post = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::edit
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit'
*/
export const edit = (args: { course: string | number, section: string | number, quiz: string | number, question: string | number } | [course: string | number, section: string | number, quiz: string | number, question: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::edit
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit'
*/
edit.url = (args: { course: string | number, section: string | number, quiz: string | number, question: string | number } | [course: string | number, section: string | number, quiz: string | number, question: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            quiz: args[2],
            question: args[3],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: args.course,
        section: args.section,
        quiz: args.quiz,
        question: args.question,
    }

    return edit.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace('{question}', parsedArgs.question.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::edit
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit'
*/
edit.get = (args: { course: string | number, section: string | number, quiz: string | number, question: string | number } | [course: string | number, section: string | number, quiz: string | number, question: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::edit
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit'
*/
edit.head = (args: { course: string | number, section: string | number, quiz: string | number, question: string | number } | [course: string | number, section: string | number, quiz: string | number, question: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::edit
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit'
*/
const editForm = (args: { course: string | number, section: string | number, quiz: string | number, question: string | number } | [course: string | number, section: string | number, quiz: string | number, question: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::edit
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit'
*/
editForm.get = (args: { course: string | number, section: string | number, quiz: string | number, question: string | number } | [course: string | number, section: string | number, quiz: string | number, question: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::edit
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:0
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}/edit'
*/
editForm.head = (args: { course: string | number, section: string | number, quiz: string | number, question: string | number } | [course: string | number, section: string | number, quiz: string | number, question: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Instructor\QuizQuestionController::update
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:65
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
export const update = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::update
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:65
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
update.url = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            quiz: args[2],
            question: args[3],
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
        question: typeof args.question === 'object'
        ? args.question.id
        : args.question,
    }

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace('{question}', parsedArgs.question.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::update
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:65
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
update.put = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::update
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:65
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
update.patch = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::update
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:65
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
const updateForm = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::update
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:65
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
updateForm.put = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::update
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:65
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
updateForm.patch = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Instructor\QuizQuestionController::destroy
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:103
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
export const destroy = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::destroy
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:103
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
destroy.url = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            section: args[1],
            quiz: args[2],
            question: args[3],
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
        question: typeof args.question === 'object'
        ? args.question.id
        : args.question,
    }

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{section}', parsedArgs.section.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace('{question}', parsedArgs.question.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::destroy
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:103
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
destroy.delete = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::destroy
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:103
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
const destroyForm = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizQuestionController::destroy
* @see app/Http/Controllers/Instructor/QuizQuestionController.php:103
* @route '/instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}'
*/
destroyForm.delete = (args: { course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } } | [course: number | { id: number }, section: number | { id: number }, quiz: number | { id: number }, question: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const QuizQuestionController = { create, store, edit, update, destroy }

export default QuizQuestionController