import ProfileController from './ProfileController'
import SecurityController from './SecurityController'
import LogoController from './LogoController'

const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
    SecurityController: Object.assign(SecurityController, SecurityController),
    LogoController: Object.assign(LogoController, LogoController),
}

export default Settings