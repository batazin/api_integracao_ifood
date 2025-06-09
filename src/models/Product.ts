class Product {
    id: string;
    name: string;
    description: string;
    price: number;
    category: string;
    stock: number;

    constructor(id: string, name: string, description: string, price: number, category: string, stock: number) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.price = price;
        this.category = category;
        this.stock = stock;
    }
}