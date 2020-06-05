import { Action } from '@ngrx/store';
import { MessageModel } from '../../model/message';

export enum MessagesActionTypes {
  SEND = '[Messages] send',
  RECEIVED = '[Messages] receive response',
}

export class SendAction implements Action {
 readonly type = MessagesActionTypes.SEND;
  constructor(public payload: any) { }
}

export class ReceiveAction implements Action {
  readonly type = MessagesActionTypes.RECEIVED;
  constructor(public payload: any) { }
}

export type CoursesActionsUnion = SendAction | ReceiveAction;
