import ScheduleController from './ScheduleController'
import BookingController from './BookingController'

const Companion = {
    ScheduleController: Object.assign(ScheduleController, ScheduleController),
    BookingController: Object.assign(BookingController, BookingController),
}

export default Companion