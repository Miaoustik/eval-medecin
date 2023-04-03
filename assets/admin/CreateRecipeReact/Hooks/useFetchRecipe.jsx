import {useState} from "react";
import useFetch from "./useFetch";

export default function (recipeid, controllerRef) {

    const [recipeId, setRecipeId] = useState(recipeid)
    const {data, loading} = useFetch(recipeid ? '/admin/modifier-recette/' + recipeId + '/data' : '/admin/creer-recette/data', controllerRef)

    return {
        data,
        loading,
        recipeId,
        setRecipeId
    }
}