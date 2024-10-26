export type Pokemon = {
  id?: number;
  name: string;
  height: number;
  weight: number;
  types: string[];
  abilities: string[];
  imageUrl?: string;
  frontDefault?: string;
}
