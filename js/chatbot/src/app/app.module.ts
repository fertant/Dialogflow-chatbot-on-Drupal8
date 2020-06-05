import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';

import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgxSpinnerModule } from 'ngx-spinner';
import { MDBBootstrapModule } from 'angular-bootstrap-md';
import { StoreModule } from '@ngrx/store';
import { EffectsModule } from '@ngrx/effects';
import { StoreDevtoolsModule } from '@ngrx/store-devtools';

import { SharedModule } from './shared/shared.module';
import { ChatModule } from './chat/chat.module';
import { NO_ERRORS_SCHEMA } from '@angular/core';
import { reducers } from './shared/store/reducers/message.reducer';
import { MessagesEffects } from './shared/store/effects/messages.effects';

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    BrowserAnimationsModule,
    NgxSpinnerModule,
    SharedModule,
    ChatModule,
    MDBBootstrapModule,
    StoreModule.forRoot(reducers),
    EffectsModule.forRoot([
      MessagesEffects
    ]),
    StoreDevtoolsModule.instrument({
      maxAge: 25
    })
  ],
  schemas: [ NO_ERRORS_SCHEMA ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
