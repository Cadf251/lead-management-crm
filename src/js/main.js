import { initEventDelegation } from "./core/event-delegation";
import { initWarningDelegation } from "./ui/warning";
import { initNav } from "./ui/nav";
import { initOverlay } from "./ui/overlay";
import { initFormUi } from "./ui/form";
import { initColaboradorForm } from "./ui/novo-colaborador-form";
import { initTooltip } from "./ui/tooltips";

initNav();
initOverlay();
initWarningDelegation();
initEventDelegation();
initFormUi();
initTooltip();