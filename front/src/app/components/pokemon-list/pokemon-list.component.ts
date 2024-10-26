import { Component, OnInit } from '@angular/core';
import { PokemonService } from "../../services/pokemon.service";
import { FormsModule } from "@angular/forms";
import { RouterLink } from "@angular/router";
import { CommonModule } from "@angular/common";
import {Pokemon} from "../../utils/types/pokemonType";

@Component({
  selector: 'app-pokemon-list',
  templateUrl: './pokemon-list.component.html',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    RouterLink,
  ],
  styleUrls: ['./pokemon-list.component.css']
})
export class PokemonListComponent implements OnInit {
  pokemons: Pokemon[] = [];
  selectedPokemon: Pokemon | null = null;
  searchTerm: string = '';

  constructor(private pokemonService: PokemonService) {}

  ngOnInit(): void {
    this.loadPokemons();
  }

  loadPokemons(): void {
    this.pokemonService.getAllPokemons().subscribe({
      next: (data) => this.pokemons = data,
      error: (err) => console.error('Erreur lors du chargement des Pokémon', err)
    });
  }


  selectPokemon(pokemon: Pokemon): void {
    this.selectedPokemon = pokemon;
    if (!pokemon.abilities.length || !pokemon.types.length) {
      this.pokemonService.getPokemonById(pokemon.id!).subscribe({
        next: (data) => this.selectedPokemon = data,
        error: (err) => console.error('Erreur lors du chargement des détails du Pokémon', err)
      });
    }
  }


  deletePokemon(id: number | undefined): void {
    this.pokemonService.deletePokemon(id).subscribe({
      next: () => {
        this.loadPokemons();
        this.selectedPokemon = null;
      },
      error: (err) => console.error('Erreur lors de la suppression du Pokémon', err)
    });
  }


  search(): void {
    if (this.searchTerm.trim()) {
      this.pokemonService.searchPokemons(this.searchTerm).subscribe({
        next: (data) => this.pokemons = data,
        error: (err) => console.error('Erreur lors de la recherche des Pokémon', err)
      });
    } else {
      this.loadPokemons();
    }
  }
}
