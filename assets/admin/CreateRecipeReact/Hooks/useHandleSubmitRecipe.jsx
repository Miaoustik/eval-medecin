import {useCallback, useMemo, useState} from "react";
import {capitalizeFirstLetter} from "../Utils/functions";

export default function (
    titleProps,
    descriptionProps,
    allergensProps,
    dietsProps,
    prepsProps,
    ingredientsProps,
    stageProps,
    controllerRef,
    errorRef,
    recipeid = null,
    setRecipeId = null,
    recipeId
) {

    const [postLoading, setPostLoading] = useState(false)
    const [trySubmitted, setTrySubmitted] = useState(false)
    const [submitted, setSubmitted] = useState(false)
    const [createdId, setCreatedId] = useState(null);

    const handleTrySubmit = useCallback((e) => {
        e.preventDefault()
        setTrySubmitted(true)
        setSubmitted(false)
    }, [])

    const handleTryNo = useCallback(() => {
        setTrySubmitted(false)
    }, [])

    const handleResetForm = useCallback(() => {

    }, [])


    const handleTryYes = useCallback((e) => {
        e.preventDefault()
        setSubmitted(false)
        if (errorRef.current === 0) {
            setPostLoading(true)
            function setAllergensDiets (entityProps) {
                const newArray = []
                Object.entries(entityProps.state).forEach(v => {
                    if (v[1]) {
                        newArray.push(v[0])
                    }
                })
                return newArray
            }

            const recipeObj = {
                title: capitalizeFirstLetter(titleProps.value.trim()),
                description: capitalizeFirstLetter(descriptionProps.value.trim()),
                allergens: setAllergensDiets(allergensProps),
                diets: setAllergensDiets(dietsProps),
                repos: prepsProps.state.repos === '' ? 0 : parseInt(prepsProps.state.repos, 10),
                cuisson: prepsProps.state.cuisson === '' ? 0 : parseInt(prepsProps.state.cuisson, 10),
                preparation: prepsProps.state.preparation === '' ? 0 : parseInt(prepsProps.state.preparation, 10),
                ingredients: ingredientsProps.state.map(e => {
                    return {
                        quantity: e.quantity,
                        name: capitalizeFirstLetter(e.name.trim())
                    }
                }),
                stages: stageProps.state.map(e => {
                    return e.value
                })
            }

            if (recipeId) {
                recipeObj['id'] = recipeId
            }

            const fetchOptions = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(recipeObj),
                signal: controllerRef.current.signal
            }

            console.log(recipeObj)

            fetch('/admin/creer-recette/create', fetchOptions)
                .then(response => response.json())
                .then(data => setRecipeId(data))
                .finally(() => {
                    setSubmitted(true)
                    setPostLoading(false)
                })
        } else {
            setTrySubmitted(false)
        }
    }, [
        titleProps,
        descriptionProps,
        allergensProps,
        dietsProps,
        prepsProps,
        ingredientsProps,
        stageProps,
        errorRef
    ])

    return useMemo(() => ({
        handleTryYes,
        postLoading,
        trySubmitted,
        submitted,
        handleTrySubmit,
        handleTryNo,
        createdId,
        handleResetForm
    }), [
        handleTryYes,
        postLoading,
        trySubmitted,
        submitted,
        handleTrySubmit,
        handleTryNo,
        createdId,
        handleResetForm
    ])
}