import { alterFunction } from "./alter-function";
import { alterTime } from "./alter-time";
import { alterReceiving } from "./receiving";
import { remove } from "./remove";

export default {
  "teamuser:alter-function" : alterFunction,
  "teamuser:receiving" : alterReceiving,
  "teamuser:changetime": alterTime,
  "teamuser:remove" : remove
};