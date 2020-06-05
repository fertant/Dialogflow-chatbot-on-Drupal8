import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MessagesService } from './services/messages.service';

@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    MessagesService
  ]
})
export class SharedModule { }
