import { Sequelize } from 'sequelize';

const connectDatabase = () => {
    const sequelize = new Sequelize(process.env.DB_NAME, process.env.DB_USER, process.env.DB_PASSWORD, {
        host: process.env.DB_HOST,
        dialect: 'postgres', // or any other dialect you are using
    });

    sequelize.authenticate()
        .then(() => {
            console.log('Connection to the database has been established successfully.');
        })
        .catch(err => {
            console.error('Unable to connect to the database:', err);
        });

    return sequelize;
};

export default connectDatabase;