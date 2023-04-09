import {useState} from "react";
import useFetch from "./useFetch";

export default function (recipeid, controllerRef) {

    const [recipeId, setRecipeId] = useState(recipeid)
    const {data, loading, setRefresh} = useFetch(recipeid ? '/admin/api/modifier-recette/' + recipeId + '/data' : '/admin/api/creer-recette/data', controllerRef)

    return {
        data,
        loading,
        recipeId,
        setRecipeId,
        setRefresh
    }
}