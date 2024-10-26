import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Pokemon } from "../utils/types/pokemonType";

@Injectable({
  providedIn: 'root'
})
export class PokemonService {
  private baseUrl = 'http://localhost:8000';

  constructor(private http: HttpClient) {}


  getAllPokemons(): Observable<Pokemon[]> {
    return this.http.get<Pokemon[]>(`${this.baseUrl}`).pipe(
      catchError(this.handleError)
    );
  }

  getPokemonById(id: number): Observable<Pokemon> {
    return this.http.get<Pokemon>(`${this.baseUrl}/pokemon/${id}`).pipe(
      catchError(this.handleError)
    );
  }

  addPokemon(pokemon: Pokemon): Observable<Pokemon> {
    return this.http.post<Pokemon>(`${this.baseUrl}/pokemon/add`, pokemon).pipe(
      catchError(this.handleError)
    );
  }


  editPokemon(pokemon: Pokemon): Observable<Pokemon> {
    if (!pokemon.id) {
      throw new Error('ID manquant pour la modification du Pokémon');
    }
    return this.http.put<Pokemon>(`${this.baseUrl}/pokemon/edit/${pokemon.id}`, pokemon).pipe(
      catchError(this.handleError)
    );
  }

  deletePokemon(id: number | undefined): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/pokemon/delete/${id}`).pipe(
      catchError(this.handleError)
    );
  }


  searchPokemons(searchTerm: string): Observable<Pokemon[]> {
    return this.http.post<Pokemon[]>(`${this.baseUrl}/pokemon/search`, { pokemonNameOrId: searchTerm }).pipe(
      catchError(this.handleError)
    );
  }


  private handleError(error: HttpErrorResponse) {
    let errorMessage = 'Une erreur s\'est produite';
    if (error.error instanceof ErrorEvent) {
      errorMessage = `Erreur: ${error.error.message}`;
    } else {
      errorMessage = `Code d'erreur : ${error.status}, Message : ${error.message}`;
    }
    console.error(errorMessage);
    return throwError(errorMessage);
  }
}
