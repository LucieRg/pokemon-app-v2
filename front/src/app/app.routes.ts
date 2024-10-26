import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { PokemonListComponent } from './components/pokemon-list/pokemon-list.component';
import { PokemonFormComponent } from './components/pokemon-form/pokemon-form.component';

export const routes: Routes = [
  { path: '', component: PokemonListComponent },
  { path: 'pokemon/add', component: PokemonFormComponent },
  { path: 'pokemon/edit/:id', component: PokemonFormComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}
