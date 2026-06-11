import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:34
* @route '/admin/quiz-answers/{answer}/grade'
*/
export const grade = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: grade.url(args, options),
    method: 'post',
})

grade.definition = {
    methods: ["post"],
    url: '/admin/quiz-answers/{answer}/grade',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:34
* @route '/admin/quiz-answers/{answer}/grade'
*/
grade.url = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { answer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { answer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            answer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        answer: typeof args.answer === 'object'
        ? args.answer.id
        : args.answer,
    }

    return grade.definition.url
            .replace('{answer}', parsedArgs.answer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:34
* @route '/admin/quiz-answers/{answer}/grade'
*/
grade.post = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: grade.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:34
* @route '/admin/quiz-answers/{answer}/grade'
*/
const gradeForm = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: grade.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:34
* @route '/admin/quiz-answers/{answer}/grade'
*/
gradeForm.post = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: grade.url(args, options),
    method: 'post',
})

grade.form = gradeForm

const answer = {
    grade: Object.assign(grade, grade),
}

export default answer