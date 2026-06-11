import CourseController from './CourseController'
import SectionController from './SectionController'
import LectureController from './LectureController'
import QuizController from './QuizController'
import QuizQuestionController from './QuizQuestionController'
import QuizGradeController from './QuizGradeController'

const Instructor = {
    CourseController: Object.assign(CourseController, CourseController),
    SectionController: Object.assign(SectionController, SectionController),
    LectureController: Object.assign(LectureController, LectureController),
    QuizController: Object.assign(QuizController, QuizController),
    QuizQuestionController: Object.assign(QuizQuestionController, QuizQuestionController),
    QuizGradeController: Object.assign(QuizGradeController, QuizGradeController),
}

export default Instructor