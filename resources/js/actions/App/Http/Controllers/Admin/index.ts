import UserController from './UserController'
import CategoryController from './CategoryController'
import CourseController from './CourseController'
import CourseEnrollmentController from './CourseEnrollmentController'
import ScheduleController from './ScheduleController'
import OrderController from './OrderController'
import CompanionController from './CompanionController'
import BookingController from './BookingController'
import ReportController from './ReportController'
import ActivityLogController from './ActivityLogController'
import ArticleController from './ArticleController'
import QuizGradeController from './QuizGradeController'

const Admin = {
    UserController: Object.assign(UserController, UserController),
    CategoryController: Object.assign(CategoryController, CategoryController),
    CourseController: Object.assign(CourseController, CourseController),
    CourseEnrollmentController: Object.assign(CourseEnrollmentController, CourseEnrollmentController),
    ScheduleController: Object.assign(ScheduleController, ScheduleController),
    OrderController: Object.assign(OrderController, OrderController),
    CompanionController: Object.assign(CompanionController, CompanionController),
    BookingController: Object.assign(BookingController, BookingController),
    ReportController: Object.assign(ReportController, ReportController),
    ActivityLogController: Object.assign(ActivityLogController, ActivityLogController),
    ArticleController: Object.assign(ArticleController, ArticleController),
    QuizGradeController: Object.assign(QuizGradeController, QuizGradeController),
}

export default Admin