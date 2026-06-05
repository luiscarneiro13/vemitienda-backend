import { Router } from "express"
import { ReminderController } from "../controllers/index.js"
import { mdAuth } from "../middlewares/index.js"

const router = Router()

router.get("/reminder", mdAuth.asureAuth, ReminderController.getAll)
router.post("/reminder", mdAuth.asureAuth, ReminderController.create)
router.get("/reminder/:id", mdAuth.asureAuth, ReminderController.getById)
router.patch("/reminder/:id", mdAuth.asureAuth, ReminderController.update)
router.delete("/reminder/:id", mdAuth.asureAuth, ReminderController.remove)

export const reminderRoutes = router
