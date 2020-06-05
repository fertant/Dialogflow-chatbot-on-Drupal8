import { ActionReducerMap, Action } from '@ngrx/store';

export const RECEIVED = 'received';

export function messageReducer(
  store: boolean = false,
  action: Action
): boolean {
  switch (action.type) {
    case RECEIVED:
      return store;
    default:
      return store;
  }
}

export const reducers: ActionReducerMap<any> = {
  message: messageReducer,
};
